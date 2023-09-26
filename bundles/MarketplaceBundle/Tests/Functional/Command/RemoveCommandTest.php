<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Tests\Functional\Command;

use Autoborna\CoreBundle\Helper\ComposerHelper;
use Autoborna\CoreBundle\Test\AbstractAutobornaTestCase;
use Autoborna\MarketplaceBundle\Command\RemoveCommand;
use Autoborna\MarketplaceBundle\Model\ConsoleOutputModel;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;

final class RemoveCommandTest extends AbstractAutobornaTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&LoggerInterface
     */
    private $logger;
    private string $packageName;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger      = $this->createMock(LoggerInterface::class);
        $this->packageName = 'koco/autoborna-recaptcha-bundle';
    }

    public function testRemoveCommand(): void
    {
        $composer    = $this->createMock(ComposerHelper::class);
        $composer->method('remove')
            ->with($this->packageName)
            ->willReturn(new ConsoleOutputModel(0, 'OK'));
        $composer->method('getAutobornaPluginPackages')
            ->willReturn(['koco/autoborna-recaptcha-bundle']);
        $command = new RemoveCommand($composer, $this->logger);

        $result = $this->testSymfonyCommand(
            'autoborna:marketplace:remove',
            ['package' => $this->packageName],
            $command
        );

        Assert::assertSame(0, $result->getStatusCode());
    }

    public function testRemoveCommandWithInvalidPackageType(): void
    {
        $composer    = $this->createMock(ComposerHelper::class);
        $composer->method('remove')
            ->with($this->packageName)
            ->willReturn(new ConsoleOutputModel(0, 'OK'));
        $composer->method('getAutobornaPluginPackages')
            ->willReturn([]);
        $command = new RemoveCommand($composer, $this->logger);

        $result = $this->testSymfonyCommand(
            'autoborna:marketplace:remove',
            ['package' => $this->packageName],
            $command
        );

        Assert::assertSame(1, $result->getStatusCode());
    }

    public function testRemoveCommandWithComposerError(): void
    {
        $composer    = $this->createMock(ComposerHelper::class);
        $composer->method('remove')
            ->with($this->packageName)
            ->willReturn(new ConsoleOutputModel(1, 'Error while removing package'));
        $composer->method('getAutobornaPluginPackages')
            ->willReturn([]);
        $command = new RemoveCommand($composer, $this->logger);

        $result = $this->testSymfonyCommand(
            'autoborna:marketplace:remove',
            ['package' => $this->packageName],
            $command
        );

        Assert::assertSame(1, $result->getStatusCode());
    }
}
