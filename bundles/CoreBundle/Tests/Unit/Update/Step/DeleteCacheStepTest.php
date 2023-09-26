<?php

namespace Autoborna\CoreBundle\Tests\Unit\Update\Step;

use Autoborna\CoreBundle\Helper\CacheHelper;
use Autoborna\CoreBundle\Update\Step\DeleteCacheStep;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Translation\TranslatorInterface;

class DeleteCacheStepTest extends AbstractStepTest
{
    /**
     * @var MockObject|CacheHelper
     */
    private $cacheHelper;

    /**
     * @var MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var DeleteCacheStep
     */
    private $step;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheHelper = $this->createMock(CacheHelper::class);
        $this->translator  = $this->createMock(TranslatorInterface::class);
        $this->step        = new DeleteCacheStep($this->cacheHelper, $this->translator);
    }

    public function testCacheIsNukedAndProgressNoted()
    {
        $stepOutput = 'autoborna.core.update.clear.cache';
        $this->translator->expects($this->once())
            ->method('trans')
            ->with($stepOutput)
            ->willReturn($stepOutput);

        $this->cacheHelper->expects($this->once())
            ->method('nukeCache');

        $this->step->execute($this->progressBar, $this->input, $this->output);

        $this->assertEquals($stepOutput, $this->progressBar->getMessage());
    }
}
