<?php

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

/**
 * Removing 2 columns that were copied from other similar part of Autoborna and never used.
 */
class Version20191126093923 extends AbstractAutobornaMigration
{
    /**
     * @throws SkipMigration
     * @throws SchemaException
     */
    public function preUp(Schema $schema): void
    {
        $table = $schema->getTable($this->prefix.'companies_leads');

        if (!$table->hasColumn('manually_added')) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE {$this->prefix}companies_leads DROP manually_added");
        $this->addSql("ALTER TABLE {$this->prefix}companies_leads DROP manually_removed");
    }
}
