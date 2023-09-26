<?php

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

/**
 * Migration.
 */
class Version20200227110431 extends AbstractAutobornaMigration
{
    /**
     * @throws SkipMigration
     * @throws SchemaException
     */
    public function preUp(Schema $schema): void
    {
        $table = $schema->getTable($this->prefix.'lead_donotcontact');

        if ($table->hasIndex($this->prefix.'dnc_channel_id_search')) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX '.$this->prefix.'dnc_channel_id_search ON '.$this->prefix.'lead_donotcontact (channel_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX '.$this->prefix.'dnc_channel_id_search ON '.$this->prefix.'lead_donotcontact');
    }
}
