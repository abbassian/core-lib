<?php

declare(strict_types=1);

namespace Autoborna\UserBundle\Tests\Functional\Controller;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;

class PublicControllerTest extends AutobornaMysqlTestCase
{
    /**
     * Tests to ensure that xss is prevented on password reset page.
     */
    public function testXssFilterOnPasswordReset(): void
    {
        $this->client->request('GET', '/passwordreset?bundle=%27-alert("XSS%20TEST%20Autoborna")-%27');
        $clientResponse = $this->client->getResponse();
        $this->assertSame(200, $clientResponse->getStatusCode(), 'Return code must be 200.');
        $responseData = $clientResponse->getContent();
        // Tests that actual string is not present.
        $this->assertStringNotContainsString('-alert("xss test autoborna")-', $responseData, 'XSS injection attempt is filtered.');
        // Tests that sanitized string is passed.
        $this->assertStringContainsString('alertxsstestautoborna', $responseData, 'XSS sanitized string is present.');
    }
}
