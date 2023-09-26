<?php

declare(strict_types=1);

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

final class Version20210614151138 extends AbstractAutobornaMigration
{
    /**
     * @throws SkipMigration
     */
    public function preUp(Schema $schema): void
    {
    }

    public function up(Schema $schema): void
    {
        // Please modify to your needs
        $this->addSql("ALTER TABLE {$this->prefix}webhooks MODIFY webhook_url LONGTEXT NOT NULL");
    }

    public function down(Schema $schema): void
    {
        // Roll back webhook_url to it's previous value: VARCHAR(191)
        // Trimming long values of webhook_url to fit the VARCHAR(191)
        $this->addSql("UPDATE {$this->prefix}webhooks SET webhook_url = left(webhook_url,191)");
        $this->addSql("ALTER TABLE {$this->prefix}webhooks MODIFY webhook_url VARCHAR(191) DEFAULT '' ");
    }
}
