<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\EventListener;

use Autoborna\CoreBundle\Event\TokenReplacementEvent;
use Autoborna\CoreBundle\Helper\BuilderTokenHelperFactory;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\EventListener\EmailSubscriber;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class EmailSubscriberTest extends TestCase
{
    /**
     * @dataProvider onEmailAddressReplacementProvider
     */
    public function testOnEmailAddressReplacement(string $value, string $expected): void
    {
        $contact = new Lead();
        $contact->setFields(['email2' => 'contact.a@email.address']);

        $event           = new TokenReplacementEvent($value, $contact);
        $emailSubscriber = new EmailSubscriber(
            new class() extends BuilderTokenHelperFactory {
                public function __construct()
                {
                }
            }
        );

        $emailSubscriber->onEmailAddressReplacement($event);

        Assert::assertSame($expected, $event->getContent());
    }

    /**
     * @return \Generator<string[]>
     */
    public function onEmailAddressReplacementProvider(): \Generator
    {
        yield ['{contactfield=unicorn}', ''];
        yield ['{contactfield=unicorn|default@value.email}', 'default@value.email'];
        yield ['{contactfield=email2}', 'contact.a@email.address'];
    }
}
