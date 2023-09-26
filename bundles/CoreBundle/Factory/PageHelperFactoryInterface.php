<?php

namespace Autoborna\CoreBundle\Factory;

use Autoborna\CoreBundle\Helper\PageHelperInterface;

interface PageHelperFactoryInterface
{
    public function make(string $sessionPrefix, int $page): PageHelperInterface;
}
