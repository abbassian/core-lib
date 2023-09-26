<?php

declare(strict_types=1);

namespace Autoborna\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Autoborna\CoreBundle\Doctrine\AbstractAutobornaMigration;

/**
 * Class Version20201015084627.
 */
final class Version20201015084627 extends AbstractAutobornaMigration
{
    /**
     * @var string
     */
    private $table = 'lead_fields';

    public function up(Schema $schema): void
    {
        $this->addSql(
            sprintf(
                "UPDATE %s SET label = '%s' WHERE alias = 'timezone';",
                $this->getTableName(),
                $this->getValue()
            )
        );
    }

    private function getTableName(): string
    {
        return $this->prefix.$this->table;
    }

    private function getValue(): string
    {
        return $this->container->get('translator')->trans('autoborna.lead.field.timezone');
    }
}
