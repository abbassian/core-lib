<?php

namespace Autoborna\CoreBundle\Tests\Unit\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\PathsHelper;
use Autoborna\CoreBundle\Helper\PreUpdateCheckHelper;
use Autoborna\CoreBundle\Helper\Update\Exception\LatestVersionSupportedException;
use Autoborna\CoreBundle\Helper\Update\Github\ReleaseParser;
use Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\AbstractPreUpdateCheck;
use Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\PreUpdateCheckError;
use Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\PreUpdateCheckResult;
use Autoborna\CoreBundle\Helper\UpdateHelper;
use Autoborna\CoreBundle\Release\Metadata;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class UpdateHelperTest extends TestCase
{
    /**
     * @var PathsHelper|MockObject
     */
    private $pathsHelper;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    /**
     * @var CoreParametersHelper|MockObject
     */
    private $coreParametersHelper;

    /**
     * @var Client|MockObject
     */
    private $client;

    /**
     * @var ResponseInterface|MockObject
     */
    private $response;

    /**
     * @var StreamInterface|MockObject
     */
    private $streamBody;

    /**
     * @var ReleaseParser|MockObject
     */
    private $releaseParser;

    /**
     * @var PreUpdateCheckHelper|MockObject
     */
    private $preUpdateCheckHelper;

    /**
     * @var UpdateHelper
     */
    private $helper;

    protected function setUp(): void
    {
        $this->pathsHelper = $this->createMock(PathsHelper::class);
        $this->pathsHelper->method('getSystemPath')
            ->with('cache')
            ->willReturn(__DIR__.'/resource/update/tmp');

        $this->logger               = $this->createMock(Logger::class);
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->releaseParser        = $this->createMock(ReleaseParser::class);
        $this->preUpdateCheckHelper = $this->createMock(PreUpdateCheckHelper::class);

        $this->response   = $this->createMock(ResponseInterface::class);
        $this->streamBody = $this->createMock(StreamInterface::class);
        $this->response
            ->method('getBody')
            ->willReturn($this->streamBody);
        $this->client = $this->createMock(Client::class);

        $this->helper = new UpdateHelper(
            $this->pathsHelper,
            $this->logger,
            $this->coreParametersHelper,
            $this->client, $this->releaseParser,
            $this->preUpdateCheckHelper
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Cleanup the files
        @unlink(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt');
    }

    public function testUpdatePackageFetchedAndSaved(): void
    {
        $this->response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->streamBody->expects($this->once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__.'/resource/update/update.zip'));

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'update.zip')
            ->willReturn($this->response);

        $result = $this->helper->fetchPackage('update.zip');
        $this->assertTrue(isset($result['error']));
        $this->assertFalse($result['error']);

        $updatePackage = __DIR__.'/resource/update/tmp/update.zip';
        $this->assertTrue(file_exists($updatePackage));
        @unlink($updatePackage);
    }

    public function testConnectionErrorReturnsError(): void
    {
        $this->response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(404);
        $this->response->expects($this->never())
            ->method('getBody');

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'update.zip')
            ->willReturn($this->response);

        $result = $this->helper->fetchPackage('update.zip');
        $this->assertTrue(isset($result['error']));
        $this->assertTrue($result['error']);
        $this->assertEquals('autoborna.core.updater.error.fetching.package', $result['message']);
    }

    public function testCacheIsRefreshedIfStabilityMismatches(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 100,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'alpha',
                null,
                null
            );

        $this->helper->fetchData();
    }

    public function testCacheIsRefreshedIfExpired(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                null
            );

        $this->helper->fetchData();
    }

    public function testCacheIsRefreshedIfForced(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time(),
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                null
            );

        $this->helper->fetchData(true);
    }

    public function testStatsAreSent(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $statsUrl = 'https://autoborna.org/stats';
        $this->coreParametersHelper->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['secret_key'],
                ['db_driver'],
                ['install_source', 'Autoborna'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                $statsUrl,
                'abc123',
                'mysql',
                'Autoborna',
                null
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $statsUrl,
                $this->callback(
                    function (array $options) {
                        $this->assertArrayHasKey(\GuzzleHttp\RequestOptions::FORM_PARAMS, $options);
                        $this->assertArrayHasKey(\GuzzleHttp\RequestOptions::CONNECT_TIMEOUT, $options);
                        $this->assertArrayHasKey(\GuzzleHttp\RequestOptions::HEADERS, $options);
                        // We need to send an Accept header to the stats server or we'll get 500 errors
                        $this->assertEquals(['Accept' => '*/*'], $options[\GuzzleHttp\RequestOptions::HEADERS]);

                        return true;
                    }
                )
            )->willReturn($this->response);

        $this->helper->fetchData();
    }

    public function testStatsNotSentIfDisabled(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $statsUrl = '';
        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                $statsUrl,
                null
            );

        $this->client->expects($this->never())
            ->method('request');

        $this->helper->fetchData();
    }

    public function testExceptionDoesNotGoUncaughtWhenThrownDuringUpdatingStats(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $statsUrl = 'https://autoborna.org/stats';
        $this->coreParametersHelper->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['secret_key'],
                ['db_driver'],
                ['install_source', 'Autoborna'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                $statsUrl,
                'abc123',
                'mysql',
                'Autoborna',
                null
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', $statsUrl, $this->anything())
            ->willReturnCallback(
                function (string $method, string $url, array $options) {
                    $request = $this->createMock(RequestInterface::class);

                    throw new \Exception('something bad happened');
                }
            );

        $this->logger->expects($this->once())
            ->method('error');

        $this->helper->fetchData();
    }

    public function testRequestExceptionDoesNotGoUncaughtWhenThrownDuringUpdatingStats(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $statsUrl = 'https://autoborna.org/stats';
        $this->coreParametersHelper->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['secret_key'],
                ['db_driver'],
                ['install_source', 'Autoborna'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                $statsUrl,
                'abc123',
                'mysql',
                'Autoborna',
                null
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', $statsUrl, $this->anything())
            ->willReturnCallback(
                function (string $method, string $url, array $options) {
                    $request = $this->createMock(RequestInterface::class);

                    throw new RequestException('something bad happened', $request, $this->response);
                }
            );

        $this->logger->expects($this->once())
            ->method('error');

        $this->helper->fetchData();
    }

    public function testRequestExceptionWithEmptyResponseDoesNotGoUncaughtWhenThrownDuringUpdatingStats(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $statsUrl = 'https://autoborna.org/stats';
        $this->coreParametersHelper->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['secret_key'],
                ['db_driver'],
                ['install_source', 'Autoborna'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                $statsUrl,
                'abc123',
                'mysql',
                'Autoborna',
                null
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', $statsUrl, $this->anything())
            ->willReturnCallback(
                function (string $method, string $url, array $options) {
                    $request = $this->createMock(RequestInterface::class);

                    throw new RequestException('something bad happened', $request, null);
                }
            );

        $this->logger->expects($this->once())
            ->method('error');

        $this->helper->fetchData();
    }

    public function testNoErrorIfLatestVersionInstalled(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $updateUrl = 'https://autoborna.org/update';
        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                $updateUrl
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', $updateUrl)
            ->willReturn($this->response);

        $this->response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->streamBody->expects($this->once())
            ->method('getContents')
            ->willReturn('[{"html_url": "https://github.com/10.0.1"}]');

        $this->releaseParser->expects($this->once())
            ->method('getLatestSupportedRelease')
            ->willThrowException(new LatestVersionSupportedException());

        $data = $this->helper->fetchData();
        $this->assertFalse($data['error']);
        $this->assertEquals('autoborna.core.updater.running.latest.version', $data['message']);
    }

    public function testErrorIfLatestVersionCouldNotBeDetermined(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $updateUrl = 'https://autoborna.org/update';
        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                $updateUrl
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', $updateUrl)
            ->willReturn($this->response);

        $this->response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(404);

        $this->releaseParser->expects($this->never())
            ->method('getLatestSupportedRelease');

        $this->logger->expects($this->once())
            ->method('error');

        $data = $this->helper->fetchData();
        $this->assertTrue($data['error']);
        $this->assertEquals('autoborna.core.updater.error.fetching.updates', $data['message']);
    }

    public function testErrorIfGuzzleException(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $updateUrl = 'https://autoborna.org/update';
        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                $updateUrl
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', $updateUrl)
            ->willThrowException(new RequestException('bad', $this->createMock(RequestInterface::class), $this->response));

        $this->releaseParser->expects($this->never())
            ->method('getLatestSupportedRelease');

        $this->logger->expects($this->once())
            ->method('error');

        $data = $this->helper->fetchData();
        $this->assertTrue($data['error']);
        $this->assertEquals('autoborna.core.updater.error.fetching.updates', $data['message']);
    }

    public function testErrorForAnyException(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $updateUrl = 'https://autoborna.org/update';
        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                $updateUrl
            );

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', $updateUrl)
            ->willThrowException(new \Exception());

        $this->releaseParser->expects($this->never())
            ->method('getLatestSupportedRelease');

        $this->logger->expects($this->once())
            ->method('error');

        $this->response->expects($this->never())
            ->method('getStatusCode');

        $data = $this->helper->fetchData();
        $this->assertTrue($data['error']);
        $this->assertEquals('autoborna.core.updater.error.fetching.updates', $data['message']);
    }

    public function testNoErrorIfInAppUpdatesAreDisabled(): void
    {
        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time() - 10800,
        ];
        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $this->coreParametersHelper->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                ['update_stability'],
                ['stats_update_url'],
                ['system_update_url']
            )
            ->willReturnOnConsecutiveCalls(
                'stable',
                null,
                null
            );

        $this->client->expects($this->never())
            ->method('request');

        $this->releaseParser->expects($this->never())
            ->method('getLatestSupportedRelease');

        $data = $this->helper->fetchData();
        $this->assertFalse($data['error']);
        $this->assertEquals('autoborna.core.updater.running.latest.version', $data['message']);
    }

    public function testFailingPreUpdateChecks(): void
    {
        $this->preparePreUpdateCheckTest();

        $this->preUpdateCheckHelper->expects($this->once())
            ->method('getChecks')
            ->willReturn([
                $this->getPassingPreUpdateTest(),
                $this->getFailingPreUpdateTest(),
                $this->getFailingPreUpdateTest(),
                $this->getPassingPreUpdateTest(),
            ]);

        $results = $this->helper->runPreUpdateChecks();
        $errors  = [];

        foreach ($results as $result) {
            if (!empty($result->errors)) {
                $errors = array_merge($errors, array_map(fn (PreUpdateCheckError $error) => $error->key, $result->errors));
            }
        }

        $this->assertSame(2, count($errors));
    }

    public function testPassingPreUpdateChecks(): void
    {
        $this->preparePreUpdateCheckTest();

        $this->preUpdateCheckHelper->expects($this->once())
            ->method('getChecks')
            ->willReturn([
                $this->getPassingPreUpdateTest(),
                $this->getPassingPreUpdateTest(),
                $this->getPassingPreUpdateTest(),
                $this->getPassingPreUpdateTest(),
            ]);

        $results = $this->helper->runPreUpdateChecks();
        $errors  = [];

        foreach ($results as $result) {
            if (!empty($result->errors)) {
                $errors = array_merge($errors, array_map(fn (PreUpdateCheckError $error) => $error->key, $result->errors));
            }
        }

        $this->assertSame(0, count($errors));
    }

    private function getFailingPreUpdateTest(): AbstractPreUpdateCheck
    {
        return new class() extends AbstractPreUpdateCheck {
            public function runCheck(): PreUpdateCheckResult
            {
                return new PreUpdateCheckResult(false, null, [new PreUpdateCheckError('Dummy')]);
            }
        };
    }

    private function getPassingPreUpdateTest(): AbstractPreUpdateCheck
    {
        return new class() extends AbstractPreUpdateCheck {
            public function runCheck(): PreUpdateCheckResult
            {
                return new PreUpdateCheckResult(true, null);
            }
        };
    }

    private function preparePreUpdateCheckTest(): void
    {
        $releaseMetadata = [
            'version'                           => '10.0.1',
            'stability'                         => 'stable',
            'minimum_php_version'               => '7.4.0',
            'maximum_php_version'               => '8.0.99',
            'show_php_version_warning_if_under' => '7.4.0',
            'minimum_autoborna_version'            => '3.2.0',
            'announcement_url'                  => '',
            'minimum_mysql_version'             => '5.7.14',
            'minimum_mariadb_version'           => '10.3.5',
        ];

        $cache = [
            'error'        => false,
            'message'      => 'autoborna.core.updater.update.available',
            'version'      => '10.0.1',
            'announcement' => 'https://autoborna.org',
            'package'      => 'https://autoborna.org/10.0.1/upgrade.zip',
            'stability'    => 'stable',
            'checkedTime'  => time(), // We actually want to use this cached data
            'metadata'     => new Metadata($releaseMetadata),
        ];

        file_put_contents(__DIR__.'/resource/update/tmp/lastUpdateCheck.txt', json_encode($cache));

        $this->coreParametersHelper->expects($this->once())
            ->method('get')
            ->with('update_stability')
            ->willReturn('stable');

        $this->releaseParser->expects($this->never())
            ->method('getLatestSupportedRelease');
    }
}
