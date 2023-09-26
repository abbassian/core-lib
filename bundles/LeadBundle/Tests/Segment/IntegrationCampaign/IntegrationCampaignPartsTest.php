<?php

namespace Autoborna\LeadBundle\Tests\Segment\IntegrationCampaign;

use Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts;

class IntegrationCampaignPartsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getIntegrationName()
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getCampaignId()
     */
    public function testConnectwise()
    {
        $field             = 'Connectwise::283';
        $doNotContactParts = new IntegrationCampaignParts($field);

        $this->assertSame('Connectwise', $doNotContactParts->getIntegrationName());
        $this->assertSame('283', $doNotContactParts->getCampaignId());
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getIntegrationName()
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getCampaignId()
     */
    public function testSalesforceExplicit()
    {
        $field             = 'Salesforce::22';
        $doNotContactParts = new IntegrationCampaignParts($field);

        $this->assertSame('Salesforce', $doNotContactParts->getIntegrationName());
        $this->assertSame('22', $doNotContactParts->getCampaignId());
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getIntegrationName()
     * @covers \Autoborna\LeadBundle\Segment\IntegrationCampaign\IntegrationCampaignParts::getCampaignId()
     */
    public function testSalesforceDefault()
    {
        $field             = '44';
        $doNotContactParts = new IntegrationCampaignParts($field);

        $this->assertSame('Salesforce', $doNotContactParts->getIntegrationName());
        $this->assertSame('44', $doNotContactParts->getCampaignId());
    }
}
