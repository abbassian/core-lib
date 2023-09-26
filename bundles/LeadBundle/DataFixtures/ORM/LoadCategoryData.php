<?php

namespace Autoborna\LeadBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\CategoryBundle\Entity\Category;
use Autoborna\CategoryBundle\Entity\CategoryRepository;
use Autoborna\CoreBundle\Helper\CsvHelper;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $this->entityManager->getRepository(Category::class);
        $categories   = CsvHelper::csv_to_array(__DIR__.'/fakecategorydata.csv');
        foreach ($categories as $category) {
            $categoryEntity = new Category();
            $categoryEntity->setTitle($category['categoryname']);
            $categoryEntity->setBundle($category['categorybundle']);
            $categoryEntity->setAlias($category['categoryalias']);
            $categoryEntity->setIsPublished($category['published']);
            $categoryRepo->saveEntity($categoryEntity);
        }
    }

    public function getOrder()
    {
        // TODO: Implement getOrder() method.
    }
}
