<?php

namespace Autoborna\EmailBundle\Tests\Helper;

use Autoborna\CoreBundle\Helper\CacheStorageHelper;
use Autoborna\EmailBundle\Helper\RequestStorageHelper;
use Autoborna\EmailBundle\Swiftmailer\Transport\MomentumTransport;
use Symfony\Component\HttpFoundation\Request;

class RequestStorageHelperTest extends \PHPUnit\Framework\TestCase
{
    private $cacheStorageMock;
    private $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheStorageMock = $this->createMock(CacheStorageHelper::class);
        $this->helper           = new RequestStorageHelper($this->cacheStorageMock);
    }

    public function testStoreRequest()
    {
        $payload = ['some' => 'values'];

        $this->cacheStorageMock->expects($this->once())
            ->method('set')
            ->with($this->anything(), $payload);

        $key = $this->helper->storeRequest(MomentumTransport::class, new Request([], $payload));

        $this->assertStringStartsWith(MomentumTransport::class, $key);
        $this->assertEquals(98, strlen($key));
    }

    public function testStoreRequestWithLongTansportName()
    {
        $payload           = ['some' => 'values'];
        $longTransportName = '';

        for ($i = 0; $i < 5; ++$i) {
            $longTransportName .= MomentumTransport::class;
        }

        $this->cacheStorageMock->expects($this->never())
            ->method('set');

        $this->expectException(\LengthException::class);
        $key = $this->helper->storeRequest($longTransportName, new Request([], $payload));
    }

    public function testGetRequest()
    {
        $payload = ['some' => 'values'];
        $key     = MomentumTransport::class.':webhook_request:5b43832134cfb0.36545510';

        $this->cacheStorageMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($payload);

        $request = $this->helper->getRequest($key);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($payload, $request->request->all());
    }

    public function testGetRequestIfNotFound()
    {
        $payload = ['some' => 'values'];
        $key     = MomentumTransport::class.':webhook_request:5b43832134cfb0.36545510';

        $this->cacheStorageMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(false);

        $this->expectException(\UnexpectedValueException::class);
        $this->helper->getRequest($key);
    }

    public function testGetTransportNameFromKey()
    {
        $this->assertEquals(MomentumTransport::class, $this->helper->getTransportNameFromKey('Autoborna\EmailBundle\Swiftmailer\Transport\MomentumTransport:webhook_request:5b43832134cfb0.36545510'));
    }

    /**
     * The StorageHelper will add '%autoborna.db_table_prefix%' as a prefix to each cache key.
     */
    public function testGetTransportNameFromKeyWithGlobalPrefix()
    {
        $this->assertEquals(MomentumTransport::class, $this->helper->getTransportNameFromKey('autoborna:Autoborna|EmailBundle|Swiftmailer|Transport|MomentumTransport:webhook_request:5bfbe8ce671198.00044461'));
    }
}
