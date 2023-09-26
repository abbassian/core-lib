<?php

namespace Autoborna\FormBundle\Tests\Controller;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;

class FormControllerFunctionalTest extends AutobornaMysqlTestCase
{
    protected $useCleanupRollback = false;

    /**
     * Index should return status code 200.
     */
    public function testIndexActionWhenNotFiltered(): void
    {
        $this->client->request('GET', '/s/forms');
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    /**
     * Filtering should return status code 200.
     */
    public function testIndexActionWhenFiltering(): void
    {
        $this->client->request('GET', '/s/forms?search=has%3Aresults&tmpl=list');
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    /**
     * Get form's create page.
     */
    public function testNewActionForm(): void
    {
        $this->client->request('GET', '/s/forms/new/');
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    /**
     * @see https://github.com/autoborna/autoborna/issues/10453
     */
    public function testSaveActionForm(): void
    {
        $crawler = $this->client->request('GET', '/s/forms/new/');
        $this->assertTrue($this->client->getResponse()->isOk());

        $form = $crawler->filterXPath('//form[@name="autobornaform"]')->form();
        $form->setValues(
            [
                'autobornaform[name]'        => 'Test',
                'autobornaform[renderStyle]' => '0',
            ]
        );
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isOk());

        $form = $crawler->filterXPath('//form[@name="autobornaform"]')->form();
        $form->setValues(
            [
                'autobornaform[renderStyle]' => '0',
            ]
        );

        // The form failed to save when saved for the second time with renderStyle=No.
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isOk(), $this->client->getResponse()->getContent());
        $this->assertStringNotContainsString('Internal Server Error - Expected argument of type "null or string", "boolean" given', $this->client->getResponse()->getContent());
    }
}
