<?php

declare(strict_types=1);

namespace Autoborna\ConfigBundle\Tests\Controller;

use Autoborna\ConfigBundle\Model\SysinfoModel;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;

class SysinfoControllerTest extends AutobornaMysqlTestCase
{
    public function testDbInfoIsShown(): void
    {
        /** @var SysinfoModel */
        $sysinfoModel = self::$container->get('autoborna.config.model.sysinfo');
        $dbInfo       = $sysinfoModel->getDbInfo();

        // Request sysinfo page
        $crawler = $this->client->request(Request::METHOD_GET, '/s/sysinfo');
        Assert::assertTrue($this->client->getResponse()->isOk());

        $dbVersion       = $crawler->filterXPath("//td[@id='dbinfo-version']")->text();
        $dbDriver        = $crawler->filterXPath("//td[@id='dbinfo-driver']")->text();
        $dbPlatform      = $crawler->filterXPath("//td[@id='dbinfo-platform']")->text();
        $recommendations = $crawler->filter('#recommendations');

        Assert::assertSame($dbInfo['version'], $dbVersion);
        Assert::assertSame($dbInfo['driver'], $dbDriver);
        Assert::assertSame($dbInfo['platform'], $dbPlatform);
        Assert::assertGreaterThan(0, $recommendations->count());
    }
}
