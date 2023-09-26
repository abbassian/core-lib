<?php

namespace Autoborna\CoreBundle\Factory;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\PageHelper;
use Autoborna\CoreBundle\Helper\PageHelperInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class PageHelperFactory implements PageHelperFactoryInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(SessionInterface $session, CoreParametersHelper $coreParametersHelper)
    {
        $this->session              = $session;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function make(string $sessionPrefix, int $page): PageHelperInterface
    {
        return new PageHelper($this->session, $this->coreParametersHelper, $sessionPrefix, $page);
    }
}
