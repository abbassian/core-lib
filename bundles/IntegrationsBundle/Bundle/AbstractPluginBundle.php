<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Bundle;

use Doctrine\DBAL\Schema\Schema;
use Exception;
use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\IntegrationsBundle\Migration\Engine;
use Autoborna\PluginBundle\Bundle\PluginBundleBase;
use Autoborna\PluginBundle\Entity\Plugin;

/**
 * Base Bundle class which should be extended by addon bundles.
 */
abstract class AbstractPluginBundle extends PluginBundleBase
{
    /**
     * @param array|null $metadata
     *
     * @throws Exception
     */
    public static function onPluginUpdate(Plugin $plugin, AutobornaFactory $factory, $metadata = null, ?Schema $installedSchema = null): void
    {
        $entityManager = $factory->getEntityManager();
        $tablePrefix   = (string) $factory->getParameter('autoborna.db_table_prefix');

        $migrationEngine = new Engine(
            $entityManager,
            $tablePrefix,
            __DIR__.'/../../../../plugins/'.$plugin->getBundle(),
            $plugin->getBundle()
        );

        if (method_exists(__CLASS__, 'installAllTablesIfMissing')) {
            static::installAllTablesIfMissing(
                $entityManager->getConnection()->getSchemaManager()->createSchema(),
                $tablePrefix,
                $factory,
                $metadata
            );
        }

        $migrationEngine->up();
    }
}
