<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Provider;

use Autoborna\LeadBundle\Exception\ChoicesNotFoundException;

interface FieldChoicesProviderInterface
{
    /**
     * @throws ChoicesNotFoundException
     *
     * @return mixed[]
     */
    public function getChoicesForField(string $fieldType, string $fieldAlias, string $search = ''): array;
}
