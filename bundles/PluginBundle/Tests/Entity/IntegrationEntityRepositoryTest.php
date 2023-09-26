<?php

declare(strict_types=1);

namespace Autoborna\PluginBundle\Tests\Entity;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\PluginBundle\Entity\IntegrationEntityRepository;
use PHPUnit\Framework\Assert;

/**
 * IntegrationRepository.
 */
class IntegrationEntityRepositoryTest extends AutobornaMysqlTestCase
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var IntegrationEntityRepository
     */
    private $integrationEntityRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prefix                      = self::$container->getParameter('autoborna.db_table_prefix');
        $this->integrationEntityRepository = $this->em->getRepository('AutobornaPluginBundle:IntegrationEntity');
    }

    public function testThatGetIntegrationsEntityIdReturnsCorrectValues(): void
    {
        $now                 = new \DateTimeImmutable();
        $integrationEntityId = random_int(1, 1000);
        $internalEntityId    = random_int(1, 1000);

        $this->connection->insert($this->prefix.'integration_entity', [
            'date_added'            => $now->format('Y-m-d H:i:s'),
            'integration'           => 'someIntegration',
            'integration_entity'    => 'someIntegrationEntity',
            'integration_entity_id' => $integrationEntityId,
            'internal_entity'       => 'someInternalEntity',
            'internal_entity_id'    => $internalEntityId,
            'last_sync_date'        => null,
            'internal'              => 'someInternalValue',
        ]);

        $results = $this->integrationEntityRepository->getIntegrationsEntityId(
            'someIntegration',
            'someIntegrationEntity',
            'someInternalEntity',
            [$internalEntityId],
            null,
            null,
            false,
            0,
            0,
            null
        );

        Assert::assertCount(1, $results);
        Assert::assertSame($integrationEntityId, (int) $results[0]['integration_entity_id']);
        Assert::assertSame($internalEntityId, (int) $results[0]['internal_entity_id']);
    }
}
