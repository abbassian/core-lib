<?php

declare(strict_types=1);

namespace Autoborna\Migrations;

use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

final class Version20201102133546 extends AbstractAutobornaMigration
{
    public function preUp(Schema $schema): void
    {
        $sql  = "SHOW INDEX FROM {$this->getTableName()} WHERE Key_name = '{$this->getIndexName()}';";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $found = (bool) $stmt->fetch(FetchMode::ASSOCIATIVE);
        $stmt->closeCursor();

        if (!$found) {
            throw new SkipMigration('Schema includes this migration');
        }
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE {$this->getTableName()} DROP INDEX {$this->getIndexName()};");
    }

    private function getTableName(): string
    {
        return $this->prefix.'email_assets_xref';
    }

    private function getIndexName(): string
    {
        return $this->generatePropertyName($this->getTableName(), 'idx', ['email_id']);
    }
}
