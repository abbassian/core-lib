<?php

declare(strict_types=1);

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;
use Autoborna\LeadBundle\Entity\LeadEventLog;

final class Version20201123070813 extends AbstractAutobornaMigration
{
    /**
     * @var string
     */
    private const TABLE_NAME = 'lead_event_log';

    /**
     * @throws SkipMigration
     */
    public function preUp(Schema $schema): void
    {
        $this->skipIf(
            $schema->getTable($this->getTableName())->hasIndex(LeadEventLog::INDEX_SEARCH),
            sprintf('Index %s already exists. Skipping the migration', LeadEventLog::INDEX_SEARCH)
        );
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            sprintf(
                'ALTER TABLE `%s` ADD INDEX `%s` (`bundle`,`object`,`action`,`object_id`,`date_added`)',
                $this->getTableName(),
                LeadEventLog::INDEX_SEARCH
            )
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(sprintf(
            'ALTER TABLE `%s` DROP INDEX `%s`',
            $this->getTableName(),
            LeadEventLog::INDEX_SEARCH
        ));
    }

    private function getTableName(): string
    {
        return $this->prefix.self::TABLE_NAME;
    }
}
