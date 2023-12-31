<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncJudge\Modes;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InformationChangeRequestDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\ConflictUnresolvedException;
use Autoborna\IntegrationsBundle\Sync\SyncJudge\Modes\FuzzyEvidence;
use PHPUnit\Framework\TestCase;

class FuzzyEvidenceTest extends TestCase
{
    public function testLeftWinnerWithCertainChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($leftChangeRequest, $winner);
    }

    public function testRightWinnerWithCertainChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($rightChangeRequest, $winner);
    }

    public function testLeftWinnerWithPossibleChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($leftChangeRequest, $winner);
    }

    public function testRightWinnerWithPossibleChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($rightChangeRequest, $winner);
    }

    public function testLeftWinnerWithCertainChangeDateTimeNewerThanRightPossibleChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($leftChangeRequest, $winner);
    }

    public function testRightWinnerWithCertainChangeDateTimeNewerThanLeftPossibleChangeDateTime(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:00:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $winner = FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);

        $this->assertEquals($rightChangeRequest, $winner);
    }

    public function testUnresolvedConflictExceptionThrownIfLeftCertainIsEqualToRightPossible(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }

    public function testUnresolvedConflictExceptionThrownIfRightCertainIsEqualToLeftPossible(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }

    public function testUnresolvedConflictExceptionThrownIfLeftCertainIsNull(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }

    public function testUnresolvedConflictExceptionThrownIfRightCertainIsNull(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setCertainChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }

    public function testUnresolvedConflictExceptionThrownIfLeftPossibleIsNull(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $rightChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }

    public function testUnresolvedConflictExceptionThrownIfRightPossibleIsNull(): void
    {
        $leftChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );
        $leftChangeRequest->setPossibleChangeDateTime(new \DateTimeImmutable('2018-10-08 00:01:00'));

        $rightChangeRequest = new InformationChangeRequestDAO(
            'Test',
            'Object',
            1,
            'field',
            new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'test')
        );

        $this->expectException(ConflictUnresolvedException::class);
        FuzzyEvidence::adjudicate($leftChangeRequest, $rightChangeRequest);
    }
}
