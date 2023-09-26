<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncJudge\Modes;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InformationChangeRequestDAO;

interface JudgementModeInterface
{
    public static function adjudicate(
        InformationChangeRequestDAO $leftChangeRequest,
        InformationChangeRequestDAO $rightChangeRequest
    ): InformationChangeRequestDAO;
}
