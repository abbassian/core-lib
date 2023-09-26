<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Doctrine\Provider;

use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumns;

interface GeneratedColumnsProviderInterface
{
    public function getGeneratedColumns(): GeneratedColumns;

    public function generatedColumnsAreSupported(): bool;

    public function getMinimalSupportedVersion(): string;
}
