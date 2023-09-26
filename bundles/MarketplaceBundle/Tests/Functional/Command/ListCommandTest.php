<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Tests\Functional\Command;

use Autoborna\CoreBundle\Test\AbstractAutobornaTestCase;
use Autoborna\MarketplaceBundle\Api\Connection;
use Autoborna\MarketplaceBundle\Command\ListCommand;
use Autoborna\MarketplaceBundle\DTO\Allowlist as DTOAllowlist;
use Autoborna\MarketplaceBundle\Service\Allowlist;
use Autoborna\MarketplaceBundle\Service\PluginCollector;
use PHPUnit\Framework\Assert;

final class ListCommandTest extends AbstractAutobornaTestCase
{
    public function testCommand(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getPlugins')
            ->willReturn(json_decode(file_get_contents(__DIR__.'/../../ApiResponse/list.json'), true));

        $allowlist = $this->createMock(Allowlist::class);
        $allowlist->method('getAllowlist')->willReturn(null);

        $pluginCollector = new PluginCollector($connection, $allowlist);
        $command         = new ListCommand($pluginCollector);

        $result = $this->testSymfonyCommand(
            ListCommand::NAME,
            [
                '--page'   => 1,
                '--limit'  => 5,
                '--filter' => 'autoborna',
            ],
            $command
        );

        $expected = <<<EOF
        +--------------------------------------------------------+-----------+--------+
        | name                                                   | downloads | favers |
        +--------------------------------------------------------+-----------+--------+
        | autoborna/autoborna-saelos-bundle                            | 10586     | 11     |
        | koco/autoborna-recaptcha-bundle                           | 2012      | 20     |
        |     This plugin brings reCAPTCHA integration to        |           |        |
        |     autoborna.                                            |           |        |
        | monogramm/autoborna-ldap-auth-bundle                      | 307       | 8      |
        |     This plugin enables LDAP authentication for        |           |        |
        |     autoborna.                                            |           |        |
        | maatoo/autoborna-referrals-bundle                         | 527       | 5      |
        |     This plugin enables referrals in autoborna.           |           |        |
        | thedmsgroup/autoborna-do-not-contact-extras-bundle        | 532       | 9      |
        |     Adds custom DNC list items to be added to standard |           |        |
        |     Autoborna DNC lists and creates phpne and sms         |           |        |
        |     channels                                           |           |        |
        +--------------------------------------------------------+-----------+--------+
        Total packages: 58
        Execution time:
        EOF;

        Assert::assertStringContainsString($expected, $result->getDisplay());
        Assert::assertSame(0, $result->getStatusCode());
    }

    public function testCommmandWithAllowlist(): void
    {
        $page  = 1;
        $limit = 5;
        $query = 'autoborna';

        $plugin1 = <<<EOF
        {
            "results": [
                {
                    "name": "koco\/autoborna-recaptcha-bundle",
                    "description": "This plugin brings reCAPTCHA integration to autoborna.",
                    "url": "https:\/\/packagist.org\/packages\/koco\/autoborna-recaptcha-bundle",
                    "repository": "https:\/\/github.com\/KonstantinCodes\/autoborna-recaptcha",
                    "downloads": 2012,
                    "favers": 20
                }
            ]
        }
        EOF;

        $plugin2 = <<<EOF
        {
            "results": [
                {
                    "name": "maatoo\/autoborna-referrals-bundle",
                    "description": "This plugin enables referrals in autoborna.",
                    "url": "https:\/\/packagist.org\/packages\/maatoo\/autoborna-referrals-bundle",
                    "repository": "https:\/\/github.com\/maatoo-io\/AutobornaReferralsBundle",
                    "downloads": 527,
                    "favers": 5
                }
            ]
        }
        EOF;

        $connection = $this->createMock(Connection::class);

        $connection->method('getPlugins')
            ->withConsecutive(
                [1, 1, 'koco/autoborna-recaptcha-bundle'],
                [1, 1, 'maatoo/autoborna-referrals-bundle'])
            ->willReturnOnConsecutiveCalls(
                json_decode($plugin1, true),
                json_decode($plugin2, true)
            );

        $allowlistPayload = DTOAllowlist::fromArray(json_decode(file_get_contents(__DIR__.'/../../ApiResponse/allowlist.json'), true));
        $allowlist        = $this->createMock(Allowlist::class);
        $allowlist->method('getAllowList')->willReturn($allowlistPayload);

        $pluginCollector = new PluginCollector($connection, $allowlist);
        $command         = new ListCommand($pluginCollector);

        $result = $this->testSymfonyCommand(
            ListCommand::NAME,
            [
                '--page'   => $page,
                '--limit'  => $limit,
                '--filter' => $query,
            ],
            $command
        );

        $expected = <<<EOF
        +-------------------------------------------------+-----------+--------+
        | name                                            | downloads | favers |
        +-------------------------------------------------+-----------+--------+
        | koco/autoborna-recaptcha-bundle                    | 2012      | 20     |
        |     This plugin brings reCAPTCHA integration to |           |        |
        |     autoborna.                                     |           |        |
        | maatoo/autoborna-referrals-bundle                  | 527       | 5      |
        |     This plugin enables referrals in autoborna.    |           |        |
        +-------------------------------------------------+-----------+--------+
        Total packages: 2
        Execution time:
        EOF;

        Assert::assertStringContainsString($expected, $result->getDisplay());
        Assert::assertSame(0, $result->getStatusCode());
    }
}
