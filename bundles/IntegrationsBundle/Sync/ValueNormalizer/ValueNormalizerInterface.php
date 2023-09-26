<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\ValueNormalizer;

use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;

interface ValueNormalizerInterface
{
    /**
     * @param $value
     * @param $type
     */
    public function normalizeForAutoborna(string $value, $type): NormalizedValueDAO;

    /**
     * @return mixed
     */
    public function normalizeForIntegration(NormalizedValueDAO $value);
}
