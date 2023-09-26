<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncJudge;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InformationChangeRequestDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\ConflictUnresolvedException;
use Autoborna\IntegrationsBundle\Sync\SyncJudge\Modes\BestEvidence;
use Autoborna\IntegrationsBundle\Sync\SyncJudge\Modes\FuzzyEvidence;
use Autoborna\IntegrationsBundle\Sync\SyncJudge\Modes\HardEvidence;

final class SyncJudge implements SyncJudgeInterface
{
    /**
     * @param string $mode
     *
     * @return InformationChangeRequestDAO
     *
     * @throws ConflictUnresolvedException
     */
    public function adjudicate(
        $mode,
        InformationChangeRequestDAO $leftChangeRequest,
        InformationChangeRequestDAO $rightChangeRequest
    ) {
        if ($leftChangeRequest->getNewValue() === $rightChangeRequest->getNewValue()) {
            return $leftChangeRequest;
        }

        switch ($mode) {
            case SyncJudgeInterface::HARD_EVIDENCE_MODE:
                return HardEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
            case SyncJudgeInterface::BEST_EVIDENCE_MODE:
                return BestEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
            default:
                return FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
        }
    }
}
