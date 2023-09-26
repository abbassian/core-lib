<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Controller;

use Autoborna\CoreBundle\Controller\CommonController;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\MarketplaceBundle\Security\Permissions\MarketplacePermissions;
use Autoborna\MarketplaceBundle\Service\Allowlist;
use Autoborna\MarketplaceBundle\Service\Config;
use Symfony\Component\HttpFoundation\Response;

class CacheController extends CommonController
{
    private CorePermissions $corePermissions;
    private Config $config;
    private Allowlist $allowlist;

    public function __construct(
        CorePermissions $corePermissions,
        Config $config,
        Allowlist $allowlist
    ) {
        $this->corePermissions = $corePermissions;
        $this->config          = $config;
        $this->allowlist       = $allowlist;
    }

    public function ClearAction(): Response
    {
        if (!$this->config->marketplaceIsEnabled()) {
            return $this->notFound();
        }

        if (!$this->corePermissions->isGranted(MarketplacePermissions::CAN_VIEW_PACKAGES)) {
            return $this->accessDenied();
        }

        $this->allowlist->clearCache();

        return $this->forward(
            'MarketplaceBundle:Package\List:list'
        );
    }
}
