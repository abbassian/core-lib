<?php

namespace Autoborna\CoreBundle\Factory;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Exception\FileNotFoundException;
use Autoborna\CoreBundle\Helper\DateTimeHelper;
use Autoborna\CoreBundle\Model\AbstractCommonModel;
use Autoborna\EmailBundle\Helper\MailHelper;
use Autoborna\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @deprecated 2.0 to be removed in 3.0
 */
class AutobornaFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $database;

    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get a model instance from the service container.
     *
     * @param $modelNameKey
     *
     * @return AbstractCommonModel
     *
     * @throws \InvalidArgumentException
     */
    public function getModel($modelNameKey)
    {
        return $this->container->get('autoborna.model.factory')->getModel($modelNameKey);
    }

    /**
     * Retrieves Autoborna's security object.
     *
     * @return \Autoborna\CoreBundle\Security\Permissions\CorePermissions
     */
    public function getSecurity()
    {
        return $this->container->get('autoborna.security');
    }

    /**
     * Retrieves Symfony's security context.
     *
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * Retrieves user currently logged in.
     *
     * @param bool $nullIfGuest
     *
     * @return User|null
     */
    public function getUser($nullIfGuest = false)
    {
        return $this->container->get('autoborna.helper.user')->getUser($nullIfGuest);
    }

    /**
     * Retrieves session object.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * Retrieves Doctrine EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return ($this->entityManager) ? $this->entityManager : $this->container->get('doctrine')->getManager();
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Retrieves Doctrine database connection for DBAL use.
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getDatabase()
    {
        return ($this->database) ? $this->database : $this->container->get('database_connection');
    }

    /**
     * @param $db
     */
    public function setDatabase($db)
    {
        $this->database = $db;
    }

    /**
     * Retrieves Translator.
     *
     * @return \Autoborna\CoreBundle\Translation\Translator
     */
    public function getTranslator()
    {
        if (defined('IN_MAUTIC_CONSOLE')) {
            /** @var \Autoborna\CoreBundle\Translation\Translator $translator */
            $translator = $this->container->get('translator');

            $translator->setLocale(
                $this->getParameter('locale')
            );

            return $translator;
        }

        return $this->container->get('translator');
    }

    /**
     * Retrieves serializer.
     *
     * @return \JMS\Serializer\Serializer
     */
    public function getSerializer()
    {
        return $this->container->get('jms_serializer');
    }

    /**
     * Retrieves templating service.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine
     */
    public function getTemplating()
    {
        return $this->container->get('autoborna.helper.templating')->getTemplating();
    }

    /**
     * Retrieves event dispatcher.
     *
     * @return \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher
     */
    public function getDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * Retrieves request.
     *
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    public function getRequest()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if (empty($request)) {
            //likely in a test as the request is not populated for outside the container
            $request      = Request::createFromGlobals();
            $requestStack = new RequestStack();
            $requestStack->push($request);
        }

        return $request;
    }

    /**
     * Retrieves Symfony's validator.
     *
     * @return \Symfony\Component\Validator\Validator
     */
    public function getValidator()
    {
        return $this->container->get('validator');
    }

    /**
     * Retrieves Autoborna system parameters.
     *
     * @return array
     */
    public function getSystemParameters()
    {
        return $this->container->getParameter('autoborna.parameters');
    }

    /**
     * Retrieves a Autoborna parameter.
     *
     * @param       $id
     * @param mixed $default
     *
     * @return bool|mixed
     */
    public function getParameter($id, $default = false)
    {
        return $this->container->get('autoborna.helper.core_parameters')->get($id, $default);
    }

    /**
     * Get DateTimeHelper.
     *
     * @param string $string
     * @param string $format
     * @param string $tz
     *
     * @return DateTimeHelper
     */
    public function getDate($string = null, $format = null, $tz = 'local')
    {
        return new DateTimeHelper($string, $format, $tz);
    }

    /**
     * Get Router.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * Get the path to specified area.  Returns relative by default with the exception of cache and log
     * which will be absolute regardless of $fullPath setting.
     *
     * @param string $name
     * @param bool   $fullPath
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getSystemPath($name, $fullPath = false)
    {
        return $this->container->get('autoborna.helper.paths')->getSystemPath($name, $fullPath);
    }

    /**
     * Returns local config file path.
     *
     * @param bool $checkExists If true, returns false if file doesn't exist
     *
     * @return bool
     */
    public function getLocalConfigFile($checkExists = true)
    {
        /** @var \AppKernel $kernel */
        $kernel = $this->container->get('kernel');

        return $kernel->getLocalConfigFile($checkExists);
    }

    /**
     * Get the current environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->container->getParameter('kernel.environment');
    }

    /**
     * Returns if Symfony is in debug mode.
     *
     * @return mixed
     */
    public function getDebugMode()
    {
        return $this->container->getParameter('kernel.debug');
    }

    /**
     * returns a ThemeHelper instance for the given theme.
     *
     * @param string $theme
     * @param bool   $throwException
     *
     * @return mixed
     *
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function getTheme($theme = 'current', $throwException = false)
    {
        return $this->container->get('autoborna.helper.theme')->getTheme($theme, $throwException);
    }

    /**
     * Gets a list of installed themes.
     *
     * @param string $specificFeature limits list to those that support a specific feature
     * @param bool   $extended        returns extended information about the themes
     *
     * @return array
     */
    public function getInstalledThemes($specificFeature = 'all', $extended = false)
    {
        return $this->container->get('autoborna.helper.theme')->getInstalledThemes($specificFeature, $extended);
    }

    /**
     * Returns MailHelper wrapper for Swift_Message via $helper->message.
     *
     * @param bool $cleanSlate False to preserve current settings, i.e. to process batched emails
     *
     * @return MailHelper
     */
    public function getMailer($cleanSlate = true)
    {
        return $this->container->get('autoborna.helper.mailer')->getMailer($cleanSlate);
    }

    /**
     * Guess the IP address from current session.
     *
     * @return string
     */
    public function getIpAddressFromRequest()
    {
        return $this->container->get('autoborna.helper.ip_lookup')->getIpAddressFromRequest();
    }

    /**
     * Get an IpAddress entity for current session or for passed in IP address.
     *
     * @param string $ip
     *
     * @return IpAddress
     */
    public function getIpAddress($ip = null)
    {
        return $this->container->get('autoborna.helper.ip_lookup')->getIpAddress($ip);
    }

    /**
     * Retrieves the application's version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->container->get('kernel')->getVersion();
    }

    /**
     * Get Symfony's logger.
     *
     * @param bool|false $system
     *
     * @return \Monolog\Logger
     */
    public function getLogger($system = false)
    {
        if ($system) {
            return $this->container->get('logger');
        } else {
            return $this->container->get('monolog.logger.autoborna');
        }
    }

    /**
     * Get a autoborna helper service.
     *
     * @param $helper
     *
     * @return object
     */
    public function getHelper($helper)
    {
        switch ($helper) {
            case 'template.assets':
                return $this->container->get('templating.helper.assets');
            case 'template.slots':
                return $this->container->get('templating.helper.slots');
            case 'template.form':
                return $this->container->get('templating.helper.form');
            case 'template.translator':
                return $this->container->get('templating.helper.translator');
            case 'template.router':
                return $this->container->get('templating.helper.router');
            default:
                return $this->container->get('autoborna.helper.'.$helper);
        }
    }

    /**
     * Get's the Symfony kernel.
     *
     * @return \AppKernel
     */
    public function getKernel()
    {
        return $this->container->get('kernel');
    }

    /**
     * Get's an array of details for Autoborna core bundles.
     *
     * @param bool|false $includePlugins
     *
     * @return array|mixed
     */
    public function getAutobornaBundles($includePlugins = false)
    {
        return $this->container->get('autoborna.helper.bundle')->getAutobornaBundles($includePlugins);
    }

    /**
     * Get's an array of details for enabled Autoborna plugins.
     *
     * @return array
     */
    public function getPluginBundles()
    {
        return $this->container->get('autoborna.helper.bundle')->getPluginBundles();
    }

    /**
     * Gets an array of a specific bundle's config settings.
     *
     * @param        $bundleName
     * @param string $configKey
     * @param bool   $includePlugins
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getBundleConfig($bundleName, $configKey = '', $includePlugins = false)
    {
        return $this->container->get('autoborna.helper.bundle')->getBundleConfig($bundleName, $configKey, $includePlugins);
    }

    /**
     * @param $service
     *
     * @return bool
     */
    public function serviceExists($service)
    {
        return $this->container->has($service);
    }

    /**
     * @param $service
     *
     * @return bool
     */
    public function get($service)
    {
        if ($this->serviceExists($service)) {
            return $this->container->get($service);
        }

        return false;
    }
}
