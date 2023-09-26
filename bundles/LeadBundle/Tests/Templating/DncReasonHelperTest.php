<?php

namespace Autoborna\LeadBundle\Tests\Templating;

use Autoborna\LeadBundle\Entity\DoNotContact;
use Autoborna\LeadBundle\Exception\UnknownDncReasonException;
use Autoborna\LeadBundle\Templating\Helper\DncReasonHelper;
use Symfony\Component\Translation\TranslatorInterface;

class DncReasonHelperTest extends \PHPUnit\Framework\TestCase
{
    private $reasonTo = [
        DoNotContact::IS_CONTACTABLE => 'autoborna.lead.event.donotcontact_contactable',
        DoNotContact::UNSUBSCRIBED   => 'autoborna.lead.event.donotcontact_unsubscribed',
        DoNotContact::BOUNCED        => 'autoborna.lead.event.donotcontact_bounced',
        DoNotContact::MANUAL         => 'autoborna.lead.event.donotcontact_manual',
    ];

    private $translations = [
        'autoborna.lead.event.donotcontact_contactable'  => 'a',
        'autoborna.lead.event.donotcontact_unsubscribed' => 'b',
        'autoborna.lead.event.donotcontact_bounced'      => 'c',
        'autoborna.lead.event.donotcontact_manual'       => 'd',
    ];

    public function testToText()
    {
        foreach ($this->reasonTo as $reasonId => $translationKey) {
            $translationResult = $this->translations[$translationKey];

            $translator = $this->createMock(TranslatorInterface::class);
            $translator->expects($this->once())
                ->method('trans')
                ->with($translationKey)
                ->willReturn($translationResult);

            $dncReasonHelper = new DncReasonHelper($translator);

            $this->assertSame($translationResult, $dncReasonHelper->toText($reasonId));
        }

        $translator      = $this->createMock(TranslatorInterface::class);
        $dncReasonHelper = new DncReasonHelper($translator);
        $this->expectException(UnknownDncReasonException::class);
        $dncReasonHelper->toText(999);
    }

    public function testGetName()
    {
        $translator      = $this->createMock(TranslatorInterface::class);
        $dncReasonHelper = new DncReasonHelper($translator);
        $this->assertSame('lead_dnc_reason', $dncReasonHelper->getName());
    }
}
