<?php

namespace Autoborna\LeadBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\CategoryBundle\Entity\Category;
use Autoborna\CategoryBundle\Entity\CategoryRepository;
use Autoborna\CoreBundle\Helper\CsvHelper;
use Autoborna\LeadBundle\Entity\LeadList;
use Autoborna\LeadBundle\Entity\LeadListRepository;

class LoadCategorizedLeadListData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Category
     */

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        /** @var LeadListRepository $categoryRepo */
        $leadListRepo = $this->entityManager->getRepository(LeadList::class);
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $this->entityManager->getRepository(Category::class);

        $leadLists = CsvHelper::csv_to_array(__DIR__.'/fakecategorizedleadlistdata.csv');
        foreach ($leadLists as $leadList) {
            $category       = $categoryRepo->find($leadList['category']);
            $leadListEntity = new LeadList();
            $leadListEntity->setName($leadList['name']);
            $leadListEntity->setPublicName($leadList['publicname']);
            $leadListEntity->setAlias($leadList['alias']);
            $leadListEntity->setCategory($category);
            $leadListRepo->saveEntity($leadListEntity);
        }
    }

    public function getOrder()
    {
        // TODO: Implement getOrder() method.
    }
}
