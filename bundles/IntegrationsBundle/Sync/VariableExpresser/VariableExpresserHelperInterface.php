<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\VariableExpresser;

use Autoborna\IntegrationsBundle\Sync\DAO\Value\EncodedValueDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;

interface VariableExpresserHelperInterface
{
    public function decodeVariable(EncodedValueDAO $EncodedValueDAO): NormalizedValueDAO;

    /**
     * @param mixed $var
     */
    public function encodeVariable($var): EncodedValueDAO;
}
