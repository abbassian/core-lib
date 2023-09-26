<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Entity;

interface IdentifierFieldEntityInterface
{
    /**
     * @return string[]
     */
    public static function getDefaultIdentifierFields(): array;
}
