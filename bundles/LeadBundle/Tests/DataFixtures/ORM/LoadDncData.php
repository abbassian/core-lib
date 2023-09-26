<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\LeadBundle\Entity\DoNotContact;

class LoadDncData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $dnc = new DoNotContact();
        $dnc->setChannel('sms');
        $dnc->setReason(DoNotContact::MANUAL);
        $dnc->setDateAdded(new \DateTime());
        $dnc->setLead($this->getReference('lead-1'));

        $manager->persist($dnc);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 8;
    }
}
