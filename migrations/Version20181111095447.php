<?php

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;
use Autoborna\CoreBundle\Helper\EncryptionHelper;

class Version20181111095447 extends AbstractAutobornaMigration
{
    public function preUp(Schema $schema): void
    {
        if ($schema->getTable($this->prefix.'webhooks')->hasColumn('secret')) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    public function up(Schema $schema): void
    {
        $secret = EncryptionHelper::generateKey();
        $this->addSql("ALTER TABLE {$this->prefix}webhooks ADD secret VARCHAR(255) DEFAULT NULL");
        $this->addSql("UPDATE {$this->prefix}webhooks SET secret = '{$secret}' WHERE secret IS NULL;");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE {$this->prefix}webhooks DROP COLUMN secret");
    }
}
