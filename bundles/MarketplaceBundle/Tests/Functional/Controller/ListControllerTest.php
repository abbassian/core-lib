<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Tests\Functional\Controller;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\MarketplaceBundle\Service\Allowlist;
use Autoborna\MarketplaceBundle\Service\Config;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ListControllerTest extends AutobornaMysqlTestCase
{
    protected function setUp(): void
    {
        if ('testMarketplaceListTableWithNoAllowList' === $this->getName()) {
            $this->configParams[Config::MARKETPLACE_ALLOWLIST_URL] = '0'; // Empty string results in null for some reason.
        }

        parent::setUp();
    }

    public function testMarketplaceListTableWithNoAllowList(): void
    {
        /** @var MockHandler $handlerStack */
        $handlerStack = self::$container->get('autoborna.http.client.mock_handler');
        $handlerStack->append(
            new Response(SymfonyResponse::HTTP_OK, [], file_get_contents(__DIR__.'/../../ApiResponse/list.json'))  // Getting the package list from Packagist API.
        );

        /** @var Allowlist $allowlist */
        $allowlist = self::$container->get('marketplace.service.allowlist');
        $allowlist->clearCache();

        $crawler = $this->client->request('GET', 's/marketplace');

        Assert::assertTrue($this->client->getResponse()->isOk(), $this->client->getResponse()->getContent());

        Assert::assertSame(
            [
                'Autoborna Saelos Bundle',
                'Autoborna Recaptcha Bundle',
                'Autoborna Ldap Auth Bundle',
                'Autoborna Referrals Bundle',
                'Autoborna Do Not Contact Extras Bundle',
            ],
            array_map(
                fn (string $dirtyPackageName) => trim($dirtyPackageName),
                $crawler->filter('#marketplace-packages-table .package-name a')->extract(['_text'])
            )
        );
    }

    public function testMarketplaceListTableWithAllowList(): void
    {
        $mockResults = json_decode(file_get_contents(__DIR__.'/../../ApiResponse/list.json'), true)['results'];

        /** @var MockHandler $handlerStack */
        $handlerStack = self::$container->get('autoborna.http.client.mock_handler');
        $handlerStack->append(
            new Response(SymfonyResponse::HTTP_OK, [], file_get_contents(__DIR__.'/../../ApiResponse/allowlist.json')), // Getting Allow list from Github API.
            new Response(SymfonyResponse::HTTP_OK, [], json_encode(['results' => [$mockResults[1]]])), // autoborna-recaptcha-bundle
            new Response(SymfonyResponse::HTTP_OK, [], json_encode(['results' => [$mockResults[3]]])), // autoborna-referrals-bundle
        );

        /** @var Allowlist $allowlist */
        $allowlist = self::$container->get('marketplace.service.allowlist');
        $allowlist->clearCache();

        $crawler = $this->client->request('GET', 's/marketplace');

        Assert::assertTrue($this->client->getResponse()->isOk(), $this->client->getResponse()->getContent());

        Assert::assertSame(
            [
                'KocoCaptcha',
                'Autoborna Referrals Bundle',
            ],
            array_map(
                fn (string $dirtyPackageName) => trim($dirtyPackageName),
                $crawler->filter('#marketplace-packages-table .package-name a')->extract(['_text'])
            )
        );
    }
}
