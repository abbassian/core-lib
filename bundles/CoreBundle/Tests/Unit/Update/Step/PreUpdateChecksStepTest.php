<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Unit\Update\Step;

use Autoborna\CoreBundle\Exception\UpdateFailedException;
use Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\PreUpdateCheckError;
use Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\PreUpdateCheckResult;
use Autoborna\CoreBundle\Helper\UpdateHelper;
use Autoborna\CoreBundle\Update\Step\PreUpdateChecksStep;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class PreUpdateChecksStepTest extends AbstractStepTest
{
    /**
     * @var MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var MockObject|UpdateHelper
     */
    private $updateHelper;
    private PreUpdateChecksStep $step;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator   = $this->createMock(TranslatorInterface::class);
        $this->updateHelper = $this->createMock(UpdateHelper::class);

        $this->step = new PreUpdateChecksStep($this->translator, $this->updateHelper);
    }

    public function testUpdateFailedExceptionIfPreUpdateChecksFailed(): void
    {
        $this->updateHelper->expects($this->once())
            ->method('runPreUpdateChecks')
            ->willReturn(
                [
                    new PreUpdateCheckResult(false, null, [new PreUpdateCheckError('Dummy')]),
                ]
            );

        $this->translator->expects($this->any())
            ->method('trans')
            ->willReturn('Dummy');

        $this->expectException(UpdateFailedException::class);

        $this->step->execute($this->progressBar, $this->input, $this->output);
    }

    public function testNoExceptionIfPreUpdateChecksPassed(): void
    {
        $this->updateHelper->expects($this->once())
            ->method('runPreUpdateChecks')
            ->willReturn(
                [
                    new PreUpdateCheckResult(true, null),
                ]
            );

        $this->translator->expects($this->any())
            ->method('trans')
            ->willReturn('Dummy');

        try {
            $this->step->execute($this->progressBar, $this->input, $this->output);
            $this->assertTrue(true);
        } catch (UpdateFailedException $exception) {
            $this->fail('UpdateFailedException should not have been thrown');
        }
    }
}
