<?php

namespace Autoborna\FormBundle\Tests\Model;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Entity\LeadFieldRepository;

class FieldModelFunctionalTest extends AutobornaMysqlTestCase
{
    public function testGetObjectFieldsUnpublishedField(): void
    {
        /** @var \Autoborna\FormBundle\Model\FieldModel $fieldModel */
        $fieldModel   = self::$container->get('autoborna.form.model.field');
        $fieldsBefore = $fieldModel->getObjectFields('lead');

        /** @var LeadFieldRepository $leadFieldRepository */
        $leadFieldRepository = $this->em->getRepository(LeadField::class);
        $field               = $leadFieldRepository->findOneBy(['alias' => 'firstname']);
        $field->setIsPublished(false);
        $leadFieldRepository->saveEntity($field);

        $fieldsAfter = $fieldModel->getObjectFields('lead');

        self::assertTrue(array_key_exists('firstname', array_flip($fieldsBefore[1]['Core'])));
        self::assertFalse(array_key_exists('firstname', array_flip($fieldsAfter[1]['Core'])));
    }
}
