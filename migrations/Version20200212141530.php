<?php

declare(strict_types=1);

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

final class Version20200212141530 extends AbstractAutobornaMigration
{
    public function getDescription(): string
    {
        return 'Removes device_fingerprint column from the lead_devices table';
    }

    /**
     * @throws SkipMigration
     * @throws SchemaException
     */
    public function preUp(Schema $schema): void
    {
        if (!$schema->getTable($this->prefix.'lead_devices')->hasColumn('device_fingerprint')) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE {$this->prefix}lead_devices DROP device_fingerprint");
    }
}
