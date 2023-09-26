<?php

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200220172041 extends AbstractAutobornaMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE `{$this->prefix}categories` SET bundle = 'messages' WHERE bundle = '0';");
    }
}
