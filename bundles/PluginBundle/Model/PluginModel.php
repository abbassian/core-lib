<?php

namespace Autoborna\PluginBundle\Model;

use Doctrine\DBAL\Schema\Schema;
use Autoborna\CoreBundle\Helper\BundleHelper;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Model\FormModel;
use Autoborna\LeadBundle\Model\FieldModel;

/**
 * Class PluginModel.
 */
class PluginModel extends FormModel
{
    /**
     * @var FieldModel
     */
    protected $leadFieldModel;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * @var BundleHelper
     */
    private $bundleHelper;

    public function __construct(FieldModel $leadFieldModel, CoreParametersHelper $coreParametersHelper, BundleHelper $bundleHelper)
    {
        $this->leadFieldModel       = $leadFieldModel;
        $this->coreParametersHelper = $coreParametersHelper;
        $this->bundleHelper         = $bundleHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Autoborna\PluginBundle\Entity\PluginRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AutobornaPluginBundle:Plugin');
    }

    public function getIntegrationEntityRepository()
    {
        return $this->em->getRepository('AutobornaPluginBundle:IntegrationEntity');
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionBase()
    {
        return 'plugin:plugins';
    }

    /**
     * Get lead fields used in selects/matching.
     */
    public function getLeadFields()
    {
        return $this->leadFieldModel->getFieldList();
    }

    /**
     * Get Company fields.
     */
    public function getCompanyFields()
    {
        return $this->leadFieldModel->getFieldList(true, true, ['isPublished' => true, 'object' => 'company']);
    }

    public function saveFeatureSettings($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Loads config.php arrays for all plugins.
     *
     * @return array
     */
    public function getAllPluginsConfig()
    {
        return $this->bundleHelper->getPluginBundles();
    }

    /**
     * Loads all installed Plugin entities from database.
     *
     * @return array
     */
    public function getInstalledPlugins()
    {
        return $this->getEntities(
            [
                'index' => 'bundle',
            ]
        );
    }

    /**
     * Returns metadata for all plugins.
     *
     * @return array
     */
    public function getPluginsMetadata()
    {
        $allMetadata     = $this->em->getMetadataFactory()->getAllMetadata();
        $pluginsMetadata = [];

        foreach ($allMetadata as $meta) {
            $namespace = $meta->namespace;

            if (false !== strpos($namespace, 'AutobornaPlugin')) {
                $bundleName = preg_replace('/\\\Entity$/', '', $namespace);
                if (!isset($pluginsMetadata[$bundleName])) {
                    $pluginsMetadata[$bundleName] = [];
                }
                $pluginsMetadata[$bundleName][$meta->getName()] = $meta;
            }
        }

        return $pluginsMetadata;
    }

    /**
     * Returns all tables of installed plugins.
     *
     * @return array
     */
    public function getInstalledPluginTables(array $pluginsMetadata)
    {
        $currentSchema          = $this->em->getConnection()->getSchemaManager()->createSchema();
        $installedPluginsTables = [];

        foreach ($pluginsMetadata as $bundleName => $pluginMetadata) {
            foreach ($pluginMetadata as $meta) {
                $table = $meta->getTableName();

                if (!isset($installedPluginsTables[$bundleName])) {
                    $installedPluginsTables[$bundleName] = [];
                }

                if ($currentSchema->hasTable($table)) {
                    $installedPluginsTables[$bundleName][] = $currentSchema->getTable($table);
                }
            }
        }

        return $installedPluginsTables;
    }

    /**
     * Generates new Schema objects for all installed plugins.
     *
     * @return array
     */
    public function createPluginSchemas(array $installedPluginsTables)
    {
        $installedPluginsSchemas = [];
        foreach ($installedPluginsTables as $bundleName => $tables) {
            $installedPluginsSchemas[$bundleName] = new Schema($tables);
        }

        return $installedPluginsSchemas;
    }
}
