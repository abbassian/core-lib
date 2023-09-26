<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Tests\Functional\Command;

use Exception;
use InvalidArgumentException;
use Autoborna\CoreBundle\Helper\ComposerHelper;
use Autoborna\CoreBundle\Test\AbstractAutobornaTestCase;
use Autoborna\MarketplaceBundle\Command\InstallCommand;
use Autoborna\MarketplaceBundle\DTO\PackageDetail;
use Autoborna\MarketplaceBundle\Exception\ApiException;
use Autoborna\MarketplaceBundle\Model\ConsoleOutputModel;
use Autoborna\MarketplaceBundle\Model\PackageModel;
use PHPUnit\Framework\Assert;

final class InstallCommandTest extends AbstractAutobornaTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&ComposerHelper
     */
    private $composerHelper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&PackageModel
     */
    private $packageModel;

    private string $packageName;

    public function setUp(): void
    {
        parent::setUp();
        $this->composerHelper = $this->createMock(ComposerHelper::class);
        $this->packageModel   = $this->createMock(PackageModel::class);
        $this->packageName    = 'koco/autoborna-recaptcha-bundle';
    }

    public function testInstallCommand(): void
    {
        $this->packageModel->method('getPackageDetail')
            ->with($this->packageName)
            ->willReturn($this->getPackageDetail());

        $this->composerHelper->method('install')
            ->with($this->packageName)
            ->willReturn(new ConsoleOutputModel(0, 'OK'));

        $command = new InstallCommand($this->composerHelper, $this->packageModel);

        $result = $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $this->packageName],
            $command
        );

        Assert::assertSame(0, $result->getStatusCode());
    }

    public function testInstallCommandWithDryRun(): void
    {
        $this->packageModel->method('getPackageDetail')
            ->with($this->packageName)
            ->willReturn($this->getPackageDetail());

        $this->composerHelper->method('install')
            ->with($this->packageName)
            ->willReturn(new ConsoleOutputModel(0, 'OK'));

        $command = new InstallCommand($this->composerHelper, $this->packageModel);

        $result = $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $this->packageName, '--dry-run' => null],
            $command
        );

        Assert::assertSame(0, $result->getStatusCode());
        Assert::assertStringContainsString('dry-running this installation', $result->getDisplay());
    }

    public function testInstallCommandWithNonExistingPackage(): void
    {
        $packageName = 'autoborna/non-existent-plugin';

        $this->packageModel->method('getPackageDetail')
            ->with($packageName)
            ->willThrowException(new ApiException('Package not found', 404));

        $command = new InstallCommand($this->composerHelper, $this->packageModel);

        $this->expectException(InvalidArgumentException::class);

        $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $packageName],
            $command
        );
    }

    public function testInstallCommandWithComposerNotAvailable(): void
    {
        $packageName = 'autoborna/non-existent-plugin';

        $this->packageModel->method('getPackageDetail')
            ->with($packageName)
            ->willThrowException(new ApiException('Internal Server Error', 500));

        $command = new InstallCommand($this->composerHelper, $this->packageModel);

        $this->expectException(Exception::class);

        $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $packageName],
            $command
        );
    }

    public function testInstallCommandWithWrongPackageType(): void
    {
        $packageName                      = 'autoborna/package-with-wrong-type';
        $packageDetail                    = $this->getPackageDetail();
        $packageDetail->packageBase->type = 'non-existent-type';

        $this->packageModel->method('getPackageDetail')
            ->with($packageName)
            ->willReturn($packageDetail);

        $command = new InstallCommand($this->composerHelper, $this->packageModel);

        $this->expectException(Exception::class);

        $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $packageName],
            $command
        );
    }

    public function testInstallCommandWithFailedComposerCommand(): void
    {
        $packageName = 'autoborna/crash-package';

        $this->composerHelper->method('install')
            ->with($packageName)
            ->willReturn(new ConsoleOutputModel(1, 'Something went wrong during the installation'));

        $this->packageModel->method('getPackageDetail')
            ->with($packageName)
            ->willReturn($this->getPackageDetail());

        $command = new InstallCommand($this->composerHelper, $this->packageModel);
        $result  = $this->testSymfonyCommand(
            'autoborna:marketplace:install',
            ['package' => $packageName],
            $command
        );

        Assert::assertSame(1, $result->getStatusCode());
        Assert::assertSame("Installing autoborna/crash-package, this might take a while...\nError while installing this plugin.\nSomething went wrong during the installation\n", $result->getDisplay());
    }

    private function getPackageDetail(): PackageDetail
    {
        $payload = json_decode(file_get_contents(__DIR__.'/../../ApiResponse/detail.json'), true);

        return PackageDetail::fromArray($payload['package']);
    }
}
