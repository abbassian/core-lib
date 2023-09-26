<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Controller\Package;

use Autoborna\CoreBundle\Controller\CommonController;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\MarketplaceBundle\Model\PackageModel;
use Autoborna\MarketplaceBundle\Security\Permissions\MarketplacePermissions;
use Autoborna\MarketplaceBundle\Service\Config;
use Autoborna\MarketplaceBundle\Service\RouteProvider;
use Symfony\Component\HttpFoundation\Response;

class RemoveController extends CommonController
{
    private PackageModel $packageModel;

    private RouteProvider $routeProvider;

    private CorePermissions $corePermissions;

    private Config $config;

    public function __construct(
        PackageModel $packageModel,
        RouteProvider $routeProvider,
        CorePermissions $corePermissions,
        Config $config
    ) {
        $this->packageModel    = $packageModel;
        $this->routeProvider   = $routeProvider;
        $this->corePermissions = $corePermissions;
        $this->config          = $config;
    }

    public function viewAction(string $vendor, string $package): Response
    {
        if (!$this->config->marketplaceIsEnabled()) {
            return $this->notFound();
        }

        if (!$this->corePermissions->isGranted(MarketplacePermissions::CAN_REMOVE_PACKAGES)) {
            return $this->accessDenied();
        }

        return $this->delegateView(
            [
                'returnUrl'      => $this->routeProvider->buildListRoute(),
                'viewParameters' => [
                    'packageDetail'  => $this->packageModel->getPackageDetail("{$vendor}/{$package}"),
                ],
                'contentTemplate' => 'MarketplaceBundle:Package:remove.html.php',
                'passthroughVars' => [
                    'autobornaContent' => 'package',
                    'activeLink'    => '#autoborna_marketplace',
                    'route'         => $this->routeProvider->buildRemoveRoute($vendor, $package),
                ],
            ]
        );
    }
}
