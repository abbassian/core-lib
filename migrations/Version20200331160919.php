<?php

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200331160919 extends AbstractAutobornaMigration
{
    /**
     * @throws SkipMigration
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema): void
    {
        $formTable   = $schema->getTable($this->prefix.'forms');
        $fieldsTable = $schema->getTable($this->prefix.'form_fields');

        if ($formTable->hasColumn('progressive_profiling_limit') && $fieldsTable->hasColumn('always_display')) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema): void
    {
        $formTable = $schema->getTable($this->prefix.'forms');

        if (!$formTable->hasColumn('progressive_profiling_limit')) {
            $this->addSql('ALTER TABLE '.$this->prefix.'forms ADD progressive_profiling_limit INT(11) DEFAULT NULL;');
        }

        $fieldsTable = $schema->getTable($this->prefix.'form_fields');

        if (!$fieldsTable->hasColumn('always_display')) {
            $this->addSql('ALTER TABLE '.$this->prefix.'form_fields ADD always_display tinyint(1) DEFAULT NULL');
        }
    }
}
