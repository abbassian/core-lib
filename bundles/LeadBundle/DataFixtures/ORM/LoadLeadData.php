<?php

namespace Autoborna\LeadBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\CsvHelper;
use Autoborna\LeadBundle\Entity\CompanyLead;
use Autoborna\LeadBundle\Entity\CompanyLeadRepository;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadRepository;

class LoadLeadData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManagerInterface $entityManager, CoreParametersHelper $coreParametersHelper)
    {
        $this->entityManager        = $entityManager;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function load(ObjectManager $manager)
    {
        /** @var LeadRepository $leadRepo */
        $leadRepo        = $this->entityManager->getRepository(Lead::class);

        /** @var CompanyLeadRepository $companyLeadRepo */
        $companyLeadRepo = $this->entityManager->getRepository(CompanyLead::class);

        $today = new \DateTime();
        $leads = CsvHelper::csv_to_array(__DIR__.'/fakeleaddata.csv');

        foreach ($leads as $count => $l) {
            $key  = $count + 1;
            $lead = new Lead();
            $lead->setDateAdded($today);
            $ipAddress = new IpAddress();
            $ipAddress->setIpAddress($l['ip'], $this->coreParametersHelper->get('parameters'));
            $this->setReference('ipAddress-'.$key, $ipAddress);
            unset($l['ip']);
            $lead->addIpAddress($ipAddress);

            if ($this->hasReference('sales-user')) {
                $lead->setOwner($this->getReference('sales-user'));
            }

            foreach ($l as $col => $val) {
                $lead->addUpdatedField($col, $val);
            }

            $leadRepo->saveEntity($lead);

            $this->setReference('lead-'.$count, $lead);

            // Assign to companies in a predictable way
            $lastCharacter = (int) substr($count, -1, 1);
            if ($lastCharacter <= 3) {
                if ($this->hasReference('company-'.$lastCharacter)) {
                    $companyLead = new CompanyLead();
                    $companyLead->setLead($lead);
                    $companyLead->setCompany($this->getReference('company-'.$lastCharacter));
                    $companyLead->setDateAdded($today);
                    $companyLead->setPrimary(true);
                    $companyLeadRepo->saveEntity($companyLead);
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 5;
    }
}
