<?php

declare(strict_types=1);

namespace Autoborna\FormBundle\Tests\Event;

use Autoborna\FormBundle\Entity\Field;
use Autoborna\FormBundle\Event\FormFieldEvent;

final class FormFieldEventTest extends \PHPUnit\Framework\TestCase
{
    public function testWorkflow(): void
    {
        $field  = new Field();
        $field2 = new Field();
        $event  = new FormFieldEvent($field, true);
        $this->assertTrue($event->isNew());
        $this->assertSame($field, $event->getField());

        $event->setField($field2);

        $this->assertSame($field2, $event->getField());
    }
}
