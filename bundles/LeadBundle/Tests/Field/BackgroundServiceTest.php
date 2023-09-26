<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\Field;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Field\BackgroundService;
use Autoborna\LeadBundle\Field\CustomFieldColumn;
use Autoborna\LeadBundle\Field\Dispatcher\FieldColumnBackgroundJobDispatcher;
use Autoborna\LeadBundle\Field\Exception\AbortColumnCreateException;
use Autoborna\LeadBundle\Field\Exception\ColumnAlreadyCreatedException;
use Autoborna\LeadBundle\Field\Exception\CustomFieldLimitException;
use Autoborna\LeadBundle\Field\Exception\LeadFieldWasNotFoundException;
use Autoborna\LeadBundle\Field\LeadFieldSaver;
use Autoborna\LeadBundle\Field\Notification\CustomFieldNotification;
use Autoborna\LeadBundle\Model\FieldModel;
use PHPUnit\Framework\MockObject\MockObject;

class BackgroundServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|BackgroundService
     */
    private $backgroundService;

    /**
     * @var MockObject|FieldModel
     */
    private $fieldModel;

    /**
     * @var MockObject|CustomFieldColumn
     */
    private $customFieldColumn;

    /**
     * @var MockObject|LeadFieldSaver
     */
    private $leadFieldSaver;

    /**
     * @var MockObject|FieldColumnBackgroundJobDispatcher
     */
    private $fieldColumnBackgroundJobDispatcher;

    /**
     * @var MockObject|CustomFieldNotification
     */
    private $customFieldNotification;

    public function setUp(): void
    {
        $this->fieldModel                         = $this->createMock(FieldModel::class);
        $this->customFieldColumn                  = $this->createMock(CustomFieldColumn::class);
        $this->leadFieldSaver                     = $this->createMock(LeadFieldSaver::class);
        $this->fieldColumnBackgroundJobDispatcher = $this->createMock(FieldColumnBackgroundJobDispatcher::class);
        $this->customFieldNotification            = $this->createMock(CustomFieldNotification::class);

        $this->backgroundService = new BackgroundService(
            $this->fieldModel,
            $this->customFieldColumn,
            $this->leadFieldSaver,
            $this->fieldColumnBackgroundJobDispatcher,
            $this->customFieldNotification
        );
    }

    public function testNoLeadField(): void
    {
        $this->fieldModel->expects($this->once())
            ->method('getEntity')
            ->willReturn(null);

        $this->expectException(LeadFieldWasNotFoundException::class);
        $this->expectExceptionMessage('LeadField entity was not found');

        $this->backgroundService->addColumn(1, 3);
    }

    public function testColumnAlreadyCreated(): void
    {
        $leadField = new LeadField();
        $leadField->setColumnWasCreated();

        $this->fieldModel->expects($this->once())
            ->method('getEntity')
            ->willReturn($leadField);

        $userId = 3;
        $this->customFieldNotification->expects($this->once())
            ->method('customFieldWasCreated')
            ->with($leadField, $userId);

        $this->expectException(ColumnAlreadyCreatedException::class);
        $this->expectExceptionMessage('Column was already created');

        $this->backgroundService->addColumn(1, $userId);
    }

    public function testAbortColumnCreate(): void
    {
        $leadField = new LeadField();
        $leadField->setColumnIsNotCreated();

        $this->fieldModel->expects($this->once())
            ->method('getEntity')
            ->willReturn($leadField);

        $userId = 3;

        $this->fieldColumnBackgroundJobDispatcher->expects($this->once())
            ->method('dispatchPreAddColumnEvent')
            ->with($leadField)
            ->willThrowException(new AbortColumnCreateException('Message'));

        $this->expectException(AbortColumnCreateException::class);
        $this->expectExceptionMessage('Message');

        $this->backgroundService->addColumn(1, $userId);
    }

    public function testCustomFieldLimit(): void
    {
        $leadField = new LeadField();
        $leadField->setColumnIsNotCreated();

        $this->fieldModel->expects($this->once())
            ->method('getEntity')
            ->willReturn($leadField);

        $this->fieldColumnBackgroundJobDispatcher->expects($this->once())
            ->method('dispatchPreAddColumnEvent')
            ->with($leadField);

        $this->customFieldColumn->expects($this->once())
            ->method('processCreateLeadColumn')
            ->with($leadField, false)
            ->willThrowException(new CustomFieldLimitException('Limit'));

        $userId = 3;
        $this->customFieldNotification->expects($this->once())
            ->method('customFieldLimitWasHit')
            ->with($leadField, $userId);

        $this->expectException(CustomFieldLimitException::class);
        $this->expectExceptionMessage('Limit');

        $this->backgroundService->addColumn(1, $userId);
    }

    public function testCreateColumnWithNoError(): void
    {
        $leadField = new LeadField();
        $leadField->setColumnIsNotCreated();

        $this->fieldModel->expects($this->once())
            ->method('getEntity')
            ->willReturn($leadField);

        $this->fieldColumnBackgroundJobDispatcher->expects($this->once())
            ->method('dispatchPreAddColumnEvent')
            ->with($leadField);

        $this->customFieldColumn->expects($this->once())
            ->method('processCreateLeadColumn')
            ->with($leadField, false);

        $this->leadFieldSaver->expects($this->once())
            ->method('saveLeadFieldEntity')
            ->with($leadField, false);

        $userId = 3;
        $this->customFieldNotification->expects($this->once())
            ->method('customFieldWasCreated')
            ->with($leadField, $userId);

        $this->backgroundService->addColumn(1, $userId);

        $this->assertFalse($leadField->getColumnIsNotCreated());
    }
}
