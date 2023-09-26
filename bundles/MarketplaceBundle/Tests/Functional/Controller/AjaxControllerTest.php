<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Tests\Functional\Controller;

use Autoborna\CoreBundle\Helper\CacheHelper;
use Autoborna\CoreBundle\Helper\ComposerHelper;
use Autoborna\CoreBundle\Test\AbstractAutobornaTestCase;
use Autoborna\MarketplaceBundle\Controller\AjaxController;
use Autoborna\MarketplaceBundle\Model\ConsoleOutputModel;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AjaxControllerTest extends AbstractAutobornaTestCase
{
    public function testInstallPackageAction(): void
    {
        $request    = new Request([], [], [], [], [], [], '{"vendor":"autoborna","package":"test-plugin-bundle"}');
        $controller = $this->generateController(false);

        $response = $controller->installPackageAction($request);

        Assert::assertSame('{"success":true}', $response->getContent());
        Assert::assertSame(200, $response->getStatusCode());
    }

    public function testRemovePackageAction(): void
    {
        $request    = new Request([], [], [], [], [], [], '{"vendor":"autoborna","package":"test-plugin-bundle"}');
        $controller = $this->generateController(true);

        $response = $controller->removePackageAction($request);

        Assert::assertSame('{"success":true}', $response->getContent());
        Assert::assertSame(200, $response->getStatusCode());
    }

    private function generateController(bool $isPackageInstalled): AjaxController
    {
        $composer = $this->createMock(ComposerHelper::class);
        $composer->method('install')->willReturn(new ConsoleOutputModel(0, 'OK'));
        $composer->method('remove')->willReturn(new ConsoleOutputModel(0, 'OK'));
        $composer->method('isInstalled')->willReturn($isPackageInstalled);

        $cacheHelper = $this->createMock(CacheHelper::class);
        $cacheHelper->method('clearSymfonyCache')->willReturn(0);

        $logger = $this->createMock(LoggerInterface::class);

        $controller = new AjaxController(
            $composer,
            $cacheHelper,
            $logger
        );
        $controller->setContainer(self::$container);

        return $controller;
    }
}
