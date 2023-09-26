<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Functional\Services\SyncService;

use Doctrine\DBAL\Connection;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\InstallBundle\InstallFixtures\ORM\LeadFieldData;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncService\SyncService;
use Autoborna\IntegrationsBundle\Tests\Functional\Services\SyncService\TestExamples\Integration\ExampleIntegration;
use Autoborna\IntegrationsBundle\Tests\Functional\Services\SyncService\TestExamples\Sync\SyncDataExchange\ExampleSyncDataExchange;
use Autoborna\LeadBundle\DataFixtures\ORM\LoadLeadData;
use Autoborna\PluginBundle\Entity\Integration;

class SyncServiceTest extends AutobornaMysqlTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Populate contacts
        $this->installDatabaseFixtures([LeadFieldData::class, LoadLeadData::class]);
    }

    public function testSync(): void
    {
        $this->markTestSkipped('disabled for now');

        // Sleep one second to ensure that the modified date/time stamps of the contacts just created are in the past
        sleep(1);

        $prefix             = $this->container->getParameter('autoborna.db_table_prefix');
        $dataExchange       = new ExampleSyncDataExchange();
        $exampleIntegration = new ExampleIntegration($dataExchange);

        $settings = new Integration();
        $settings->setFeatureSettings(['sync' => ['objects' => [Contact::NAME]]]);
        $settings->setIsPublished(true);
        $exampleIntegration->setIntegrationConfiguration($settings);

        $syncIntegrationsHelper = $this->container->get('autoborna.integrations.helper.sync_integrations');
        $syncIntegrationsHelper->addIntegration($exampleIntegration);

        /** @var SyncService $syncService */
        $syncService = $this->container->get('autoborna.integrations.sync.service');

        $syncService->processIntegrationSync(ExampleIntegration::NAME, true);
        $payload = $dataExchange->getOrderPayload();

        // Created the 48 known contacts already in Autoborna
        $this->assertCount(48, $payload['create']);
        $this->assertCount(2, $payload['update']);

        $this->assertEquals(
            [
                4 => [
                        'id'         => 4,
                        'object'     => ExampleSyncDataExchange::OBJECT_LEAD,
                        'first_name' => 'Lewis',
                        'last_name'  => 'Syed',
                        'email'      => 'LewisTSyed@gustr.com',
                        'street1'    => '107 Yorkie Lane',
                    ],
                3 => [
                        'id'         => 3,
                        'object'     => ExampleSyncDataExchange::OBJECT_LEAD,
                        'first_name' => 'Nellie',
                        'last_name'  => 'Baird',
                        'email'      => 'NellieABaird@armyspy.com',
                        'street1'    => '1930 Uitsig St',
                    ],
            ],
            $payload['update']
        );

        // Validate mapping table
        /** @var Connection $connection */

        // All should be mapped to the OBJECT_LEAD object
        $qb      = $this->connection->createQueryBuilder();
        $results = $qb->select('count(*) as the_count, m.integration_object_name, m.integration')
            ->from($prefix.'sync_object_mapping', 'm')
            ->groupBy('m.integration, m.integration_object_name')
            ->execute()
            ->fetchAll();

        $this->assertCount(1, $results);
        $this->assertEquals(ExampleIntegration::NAME, $results[0]['integration']);
        $this->assertEquals(ExampleSyncDataExchange::OBJECT_LEAD, $results[0]['integration_object_name']);

        // All should be mapped to the Autoborna contact object
        $qb      = $this->connection->createQueryBuilder();
        $results = $qb->select('count(*) as the_count, m.internal_object_name, m.integration')
            ->from($prefix.'sync_object_mapping', 'm')
            ->groupBy('m.integration, m.internal_object_name')
            ->execute()
            ->fetchAll();

        $this->assertCount(1, $results);
        $this->assertEquals(ExampleIntegration::NAME, $results[0]['integration']);
        $this->assertEquals(Contact::NAME, $results[0]['internal_object_name']);

        // There should be 50 entries
        $qb      = $this->connection->createQueryBuilder();
        $results = $qb->select('count(*) as the_count')
            ->from($prefix.'sync_object_mapping', 'm')
            ->execute()
            ->fetchAll();
        $this->assertEquals(50, $results[0]['the_count']);
    }
}
