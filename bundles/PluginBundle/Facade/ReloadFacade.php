<?php

namespace Autoborna\PluginBundle\Facade;

use Autoborna\PluginBundle\Helper\ReloadHelper;
use Autoborna\PluginBundle\Model\PluginModel;
use Symfony\Component\Translation\TranslatorInterface;

class ReloadFacade
{
    private $pluginModel;
    private $reloadHelper;
    private $translator;

    public function __construct(PluginModel $pluginModel, ReloadHelper $reloadHelper, TranslatorInterface $translator)
    {
        $this->pluginModel  = $pluginModel;
        $this->reloadHelper = $reloadHelper;
        $this->translator   = $translator;
    }

    /**
     * This method finds all plguins that needs to be enabled, disabled, installed and updated
     * and do all those actions.
     *
     * Returns humanly understandable message about its doings.
     *
     * @return string
     */
    public function reloadPlugins()
    {
        $plugins                 = $this->pluginModel->getAllPluginsConfig();
        $pluginMetadata          = $this->pluginModel->getPluginsMetadata();
        $installedPlugins        = $this->pluginModel->getInstalledPlugins();
        $installedPluginTables   = $this->pluginModel->getInstalledPluginTables($pluginMetadata);
        $installedPluginsSchemas = $this->pluginModel->createPluginSchemas($installedPluginTables);
        $disabledPlugins         = $this->reloadHelper->disableMissingPlugins($plugins, $installedPlugins);
        $enabledPlugins          = $this->reloadHelper->enableFoundPlugins($plugins, $installedPlugins);
        $updatedPlugins          = $this->reloadHelper->updatePlugins($plugins, $installedPlugins, $pluginMetadata, $installedPluginsSchemas);
        $installedPlugins        = $this->reloadHelper->installPlugins($plugins, $installedPlugins, $pluginMetadata, $installedPluginsSchemas);
        $persist                 = array_values((array) ($disabledPlugins + $enabledPlugins + $updatedPlugins + $installedPlugins));

        $this->pluginModel->saveEntities($persist);

        // Alert the user to the number of additions
        return $this->translator->trans(
            'autoborna.plugin.notice.reloaded',
            [
                '%added%'    => count($installedPlugins),
                '%disabled%' => count($disabledPlugins),
                '%updated%'  => count($updatedPlugins),
            ],
            'flashes'
        );
    }
}
