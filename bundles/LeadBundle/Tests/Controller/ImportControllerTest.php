<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\Controller;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\LeadBundle\Command\ImportCommand;
use Autoborna\LeadBundle\Entity\Import;
use Autoborna\LeadBundle\Entity\ImportRepository;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Entity\LeadFieldRepository;
use Autoborna\LeadBundle\Entity\LeadRepository;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ImportControllerTest extends AutobornaMysqlTestCase
{
    public function testImportWithoutFile(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/s/contacts/import/new');
        $form    = $crawler->selectButton('Upload')->form();
        $crawler = $this->client->submit($form);

        Assert::assertStringContainsString('Please select a CSV file to upload', $crawler->html(), $crawler->html());
    }

    /**
     * Setting the phone field as required to test the validation.
     * Phone is not part of the csv fixture so it won't be auto-mapped.
     */
    public function testImportMappingRequiredFieldValidation(): void
    {
        $this->setPhoneFieldIsRequired(true);

        $crawler    = $this->client->request(Request::METHOD_GET, '/s/contacts/import/new');
        $uploadForm = $crawler->selectButton('Upload')->form();
        $file       = new UploadedFile(dirname(__FILE__).'/../Fixtures/contacts.csv', 'contacs.csv', 'itext/csv');

        $uploadForm['lead_import[file]']->setValue((string) $file);

        $crawler     = $this->client->submit($uploadForm);
        $mappingForm = $crawler->selectButton('Import')->form();
        $crawler     = $this->client->submit($mappingForm);

        Assert::assertStringContainsString('Some required fields are missing. You must map the field "Phone."', $crawler->html());
    }

    public function testImportMappingAndImport(): void
    {
        $crawler    = $this->client->request(Request::METHOD_GET, '/s/contacts/import/new');
        $uploadForm = $crawler->selectButton('Upload')->form();
        $file       = new UploadedFile(dirname(__FILE__).'/../Fixtures/contacts.csv', 'contacs.csv', 'itext/csv');

        $uploadForm['lead_import[file]']->setValue((string) $file);

        $crawler     = $this->client->submit($uploadForm);
        $mappingForm = $crawler->selectButton('Import')->form();
        $crawler     = $this->client->submit($mappingForm);

        Assert::assertStringContainsString('Import process was successfully created. You will be notified when finished.', $crawler->html());

        /** @var ImportRepository $importRepository */
        $importRepository = $this->em->getRepository(Import::class);

        /** @var Import $importEntity */
        $importEntity = $importRepository->findOneBy(['originalFile' => 'contacts.csv']);

        $fields = ['email' => 'email', 'firstname' => 'firstname', 'lastname' => 'lastname'];

        Assert::assertNotNull($importEntity);
        Assert::assertSame(2, $importEntity->getLineCount());
        Assert::assertSame(Import::QUEUED, $importEntity->getStatus());
        Assert::assertSame('lead', $importEntity->getObject());
        Assert::assertSame($fields, $importEntity->getProperties()['fields']);
        Assert::assertSame(array_values($fields), $importEntity->getProperties()['headers']);

        $this->runCommand(ImportCommand::COMMAND_NAME);

        $this->em->clear();

        /** @var Import $importEntity */
        $importEntity = $importRepository->findOneBy(['originalFile' => 'contacts.csv']);

        Assert::assertNotNull($importEntity);
        Assert::assertSame(2, $importEntity->getLineCount());
        Assert::assertSame(2, $importEntity->getInsertedCount());
        Assert::assertSame(Import::IMPORTED, $importEntity->getStatus());

        /** @var LeadRepository $importRepository */
        $leadRepository = $this->em->getRepository(Lead::class);

        /** @var Lead[] $contacts */
        $contacts = $leadRepository->findBy(['email' => ['john@doe.email', 'ferda@mravenec.email']]);

        Assert::assertCount(2, $contacts);
    }

    private function setPhoneFieldIsRequired(bool $required): void
    {
        /** @var LeadFieldRepository $fieldRepository */
        $fieldRepository = $this->em->getRepository(LeadField::class);

        /** @var LeadField $phoneField */
        $phoneField = $fieldRepository->findOneBy(['alias' => 'phone']);

        $phoneField->setIsRequired($required);
        $fieldRepository->saveEntity($phoneField);
    }
}
