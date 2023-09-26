<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Functional;

use Autoborna\CoreBundle\Test\AbstractAutobornaTestCase;
use PHPUnit\Framework\Assert;

class ParametersTest extends AbstractAutobornaTestCase
{
    public function testRememberMeParameterUsesIntProcessor(): void
    {
        Assert::assertSame(31536000, self::$container->getParameter('autoborna.rememberme_lifetime'));
    }
}
