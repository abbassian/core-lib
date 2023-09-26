<?php

namespace Autoborna\CoreBundle\Tests\Functional\Entity;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;

class CommonRepositoryTest extends AutobornaMysqlTestCase
{
    /**
     * @testdox Test that is:mine does not throw an exception due to bad DQL
     */
    public function testIsMineSearchCommandDoesntCauseExceptionDueToBadDQL()
    {
        $this->client->request('GET', 's/contacts?search=is:mine');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('is:mine', $this->client->getResponse()->getContent());
    }

    public function testIsMineSearchCommandDoesntCauseExceptionDueToBadDQLForCompanies()
    {
        $this->client->request('GET', 's/companies?search=is:mine');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('is:mine', $this->client->getResponse()->getContent());
    }

    public function testIsPublishedSearchCommandDoesntCauseExceptionDueToBadDQLForEmails()
    {
        $this->client->request('GET', 's/emails?search=is:published');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('is:published', $this->client->getResponse()->getContent());
    }
}
