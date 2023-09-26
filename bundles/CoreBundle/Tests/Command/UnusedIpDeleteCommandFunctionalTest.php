<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Command;

use Exception;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Entity\IpAddressRepository;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;

class UnusedIpDeleteCommandFunctionalTest extends AutobornaMysqlTestCase
{
    /**
     * @throws Exception
     */
    public function testUnusedIpDeleteCommand(): void
    {
        // Emulate unused IP address.
        /** @var IpAddressRepository $ipAddressRepo */
        $ipAddressRepo = $this->em->getRepository(IpAddress::class);
        $ipAddressRepo->saveEntity(new IpAddress('127.0.0.1'));
        $count = $ipAddressRepo->count(['ipAddress' => '127.0.0.1']);
        self::assertSame(1, $count);

        // Delete unused IP address.
        $this->runCommand('autoborna:unusedip:delete');

        $count = $ipAddressRepo->count(['ipAddress' => '127.0.0.1']);
        self::assertSame(0, $count);
    }
}
