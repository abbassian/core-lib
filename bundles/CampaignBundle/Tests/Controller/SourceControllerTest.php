<?php

declare(strict_types=1);

namespace Autoborna\CampaignBundle\Tests\Controller;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\FormBundle\Entity\Form;

class SourceControllerTest extends AutobornaMysqlTestCase
{
    public function testTwoSourcesWithSameName(): void
    {
        $form1 = new Form();
        $form1->setName('test');
        $form1->setAlias('test');
        $form1->setFormType('campaign');

        $form2 = new Form();
        $form2->setName('test');
        $form2->setAlias('test');
        $form2->setFormType('campaign');

        $this->em->persist($form1);
        $this->em->persist($form2);

        $this->em->flush();
        $this->em->clear();

        $this->client->request('GET', '/s/campaigns/sources/new/random_object_id?sourceType=forms', [], [], $this->createAjaxHeaders());
        $clientResponse  = $this->client->getResponse();
        $responseContent = $clientResponse->getContent();
        $this->assertSame(200, $clientResponse->getStatusCode(), $responseContent);

        $html = json_decode($responseContent, true)['newContent'];
        $this->assertStringContainsString("<option value=\"{$form1->getId()}\" >test ({$form1->getId()})</option>", $html);
        $this->assertStringContainsString("<option value=\"{$form2->getId()}\" >test ({$form2->getId()})</option>", $html);
    }
}
