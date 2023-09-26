<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Functional\Helper;

use Autoborna\CoreBundle\Helper\CommandHelper;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use PHPUnit\Framework\Assert;

class CommandHelperTest extends AutobornaMysqlTestCase
{
    /**
     * @var CommandHelper
     */
    private $commandHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandHelper = $this->getContainer()->get('autoborna.helper.command');
    }

    public function testRunCommandWithParam(): void
    {
        $response = $this->commandHelper->runCommand('help', ['--version']);
        Assert::assertSame(0, $response->getStatusCode());
        Assert::assertStringContainsString('(env: test, debug: false)', $response->getMessage());
    }

    public function testRunCommandWithoutParam(): void
    {
        $response = $this->commandHelper->runCommand('list');
        Assert::assertSame(0, $response->getStatusCode());
        Assert::assertStringContainsString('doctrine:database:create', $response->getMessage());
    }
}
