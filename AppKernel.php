<?php

use Autoborna\CoreBundle\Loader\ParameterLoader;
use Autoborna\CoreBundle\Release\ThisRelease;
use Autoborna\QueueBundle\Queue\QueueProtocol;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Autoborna Application Kernel.
 */
class AppKernel extends Kernel
{
    /**
     * @var bool|null
     */
    private $installed;

    /**
     * @var ParameterLoader|null
     */
    private $parameterLoader;

    /**
     * @param string $environment The environment
     * @param bool   $debug       Whether to enable debugging or not
     *
     * @api
     */
    public function __construct($environment, $debug)
    {
        $metadata = ThisRelease::getMetadata();

        defined('MAUTIC_ENV') or define('MAUTIC_ENV', $environment);
        defined('MAUTIC_VERSION') or define('MAUTIC_VERSION', $metadata->getVersion());

        /**
         * This is required for Doctrine's automatic database detection. When Autoborna hasn't been
         * installed yet, we don't have a database to connect to, causing automatic database platform
         * detection to fail. We use the MAUTIC_DB_SERVER_VERSION constant to temporarily set a server_version
         * if no database settings have been provided yet.
         */
        if (!defined('MAUTIC_DB_SERVER_VERSION')) {
            $localConfigFile = ParameterLoader::getLocalConfigFile($this->getRootDir(), false);
            define('MAUTIC_DB_SERVER_VERSION', file_exists($localConfigFile) ? null : '5.7');
        }

        parent::__construct($environment, $debug);
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        if (false !== strpos($request->getRequestUri(), 'installer') || !$this->isInstalled()) {
            defined('MAUTIC_INSTALLER') or define('MAUTIC_INSTALLER', 1);
        }

        if (defined('MAUTIC_INSTALLER')) {
            $uri = $request->getRequestUri();
            if (false === strpos($uri, 'installer')) {
                $base   = $request->getBaseUrl();
                $prefix = '';
                //check to see if the .htaccess file exists or if not running under apache
                if (false === stripos($request->server->get('SERVER_SOFTWARE', ''), 'apache')
                    || !file_exists(__DIR__.'../.htaccess')
                    && false === strpos(
                        $base,
                        'index'
                    )
                ) {
                    $prefix .= '/index.php';
                }

                return new RedirectResponse($request->getUriForPath($prefix.'/installer'));
            }
        }

        if (false === $this->booted) {
            $this->boot();
        }

        /*
         * If we've already sent the response headers, and we have a session
         * set in the request, set that as the session in the container.
         */
        if (headers_sent() && $request->getSession()) {
            $this->getContainer()->set('session', $request->getSession());
        }

        // Check for an an active db connection and die with error if unable to connect
        if (!defined('MAUTIC_INSTALLER')) {
            $db = $this->getContainer()->get('database_connection');
            try {
                $db->connect();
            } catch (\Exception $e) {
                error_log($e);
                throw new \Autoborna\CoreBundle\Exception\DatabaseConnectionException($this->getContainer()->get('translator')->trans('autoborna.core.db.connection.error', ['%code%' => $e->getCode()]), 0, $e);
            }
        }

        return parent::handle($request, $type, $catch);
    }

    public function registerBundles(): array
    {
        $bundles = [
            // Symfony/Core Bundles
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new LightSaml\SymfonyBridgeBundle\LightSamlSymfonyBridgeBundle(),
            new LightSaml\SpBundle\LightSamlSpBundle(),
            new Noxlogic\RateLimitBundle\NoxlogicRateLimitBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),

            // Autoborna Bundles
            new Autoborna\ApiBundle\AutobornaApiBundle(),
            new Autoborna\AssetBundle\AutobornaAssetBundle(),
            new Autoborna\CalendarBundle\AutobornaCalendarBundle(),
            new Autoborna\CampaignBundle\AutobornaCampaignBundle(),
            new Autoborna\CategoryBundle\AutobornaCategoryBundle(),
            new Autoborna\ChannelBundle\AutobornaChannelBundle(),
            new Autoborna\ConfigBundle\AutobornaConfigBundle(),
            new Autoborna\CoreBundle\AutobornaCoreBundle(),
            new Autoborna\DashboardBundle\AutobornaDashboardBundle(),
            new Autoborna\DynamicContentBundle\AutobornaDynamicContentBundle(),
            new Autoborna\EmailBundle\AutobornaEmailBundle(),
            new Autoborna\FormBundle\AutobornaFormBundle(),
            new Autoborna\InstallBundle\AutobornaInstallBundle(),
            new Autoborna\IntegrationsBundle\IntegrationsBundle(),
            new Autoborna\LeadBundle\AutobornaLeadBundle(),
            new Autoborna\MarketplaceBundle\MarketplaceBundle(),
            new Autoborna\NotificationBundle\AutobornaNotificationBundle(),
            new Autoborna\PageBundle\AutobornaPageBundle(),
            new Autoborna\PluginBundle\AutobornaPluginBundle(),
            new Autoborna\PointBundle\AutobornaPointBundle(),
            new Autoborna\ReportBundle\AutobornaReportBundle(),
            new Autoborna\SmsBundle\AutobornaSmsBundle(),
            new Autoborna\StageBundle\AutobornaStageBundle(),
            new Autoborna\StatsBundle\AutobornaStatsBundle(),
            new Autoborna\UserBundle\AutobornaUserBundle(),
            new Autoborna\WebhookBundle\AutobornaWebhookBundle(),
            new Autoborna\CacheBundle\AutobornaCacheBundle(),
        ];

        $queueProtocol = $this->getParameterLoader()->getLocalParameterBag()->get('queue_protocol', '');
        $bundles[]     = new Autoborna\QueueBundle\AutobornaQueueBundle($queueProtocol);
        switch ($queueProtocol) {
            case QueueProtocol::RABBITMQ:
                $bundles[] = new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle();
                break;
            case QueueProtocol::BEANSTALKD:
                $bundles[] = new Leezy\PheanstalkBundle\LeezyPheanstalkBundle();
                break;
        }

        // dynamically register Autoborna Plugin Bundles
        $searchPath = dirname(__DIR__).'/plugins';
        $finder     = new \Symfony\Component\Finder\Finder();
        $finder->files()
            ->followLinks()
            ->depth('1')
            ->in($searchPath)
            ->name('*Bundle.php');

        foreach ($finder as $file) {
            $dirname  = basename($file->getRelativePath());
            $filename = substr($file->getFilename(), 0, -4);

            $class = '\\AutobornaPlugin'.'\\'.$dirname.'\\'.$filename;
            if (class_exists($class)) {
                $plugin = new $class();

                if ($plugin instanceof \Symfony\Component\HttpKernel\Bundle\Bundle) {
                    if (defined($class.'::MINIMUM_MAUTIC_VERSION')) {
                        // Check if this version supports the plugin before loading it
                        if (version_compare($this->getVersion(), constant($class.'::MINIMUM_MAUTIC_VERSION'), 'lt')) {
                            continue;
                        }
                    }
                    $bundles[] = $plugin;
                }

                unset($plugin);
            }
        }

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Webfactory\Bundle\ExceptionsBundle\WebfactoryExceptionsBundle();
            $bundles[] = new Fidry\PsyshBundle\PsyshBundle();
        }

        if (in_array($this->getEnvironment(), ['test'])) {
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
            $bundles[] = new Liip\TestFixturesBundle\LiipTestFixturesBundle();
        }

        // Check for local bundle inclusion
        if (file_exists(__DIR__.'/config/bundles_local.php')) {
            include __DIR__.'/config/bundles_local.php';
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        if (true === $this->booted) {
            return;
        }

        // load parameters with defaults into the environment
        $parameterLoader = $this->getParameterLoader();
        $parameterLoader->loadIntoEnvironment();

        if (!defined('MAUTIC_TABLE_PREFIX')) {
            //set the table prefix before boot
            define('MAUTIC_TABLE_PREFIX', $parameterLoader->getLocalParameterBag()->get('db_table_prefix', ''));
        }

        // init bundles
        $this->initializeBundles();

        // init container
        $this->initializeContainer();

        // boot bundles
        foreach ($this->getBundles() as $name => $bundle) {
            $bundle->setContainer($this->container);
            $bundle->boot();
        }

        $this->booted = true;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.php');
    }

    /**
     * Retrieves the application's version number.
     */
    public function getVersion(): string
    {
        return MAUTIC_VERSION;
    }

    /**
     * Checks if the application has been installed.
     */
    protected function isInstalled(): bool
    {
        if (null === $this->installed) {
            $localParameters = $this->getParameterLoader()->getLocalParameterBag();
            $dbDriver        = $localParameters->get('db_driver');
            $mailerFromName  = $localParameters->get('mailer_from_name');

            $this->installed = !empty($dbDriver) && !empty($mailerFromName);
        }

        return $this->installed;
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getCacheDir(): string
    {
        if ($cachePath = $this->getParameterLoader()->getLocalParameterBag()->get('cache_path')) {
            $envFolder = ('/' != substr($cachePath, -1)) ? '/'.$this->environment : $this->environment;

            return str_replace('%kernel.root_dir%', $this->getRootDir(), $cachePath.$envFolder);
        }

        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir(): string
    {
        if ($logPath = $this->getParameterLoader()->getLocalParameterBag()->get('log_path')) {
            return str_replace('%kernel.root_dir%', $this->getRootDir(), $logPath);
        }

        return dirname(__DIR__).'/var/logs';
    }

    /**
     * Get local config file.
     */
    public function getLocalConfigFile(): string
    {
        $root = $this->getRootDir();

        return ParameterLoader::getLocalConfigFile($root);
    }

    private function getParameterLoader(): ParameterLoader
    {
        if ($this->parameterLoader) {
            return $this->parameterLoader;
        }

        return $this->parameterLoader = new ParameterLoader();
    }
}