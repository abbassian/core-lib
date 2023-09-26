<?php

namespace Autoborna\PluginBundle\Tests\Helper;

use Doctrine\DBAL\Schema\Schema;
use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\PluginBundle\Entity\Plugin;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * A stub Base Bundle class which implements stub methods for testing purposes.
 */
abstract class PluginBundleBaseStub extends Bundle
{
    /**
     * @param null $metadata
     * @param null $installedSchema
     *
     * @throws \Exception
     */
    public static function onPluginInstall(Plugin $plugin, AutobornaFactory $factory, $metadata = null, $installedSchema = null)
    {
    }

    /**
     * Called by PluginController::reloadAction when the addon version does not match what's installed.
     *
     * @param null   $metadata
     * @param Schema $installedSchema
     *
     * @throws \Exception
     */
    public static function onPluginUpdate(Plugin $plugin, AutobornaFactory $factory, $metadata = null, Schema $installedSchema = null)
    {
    }
}
