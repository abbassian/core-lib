<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Event;

use Autoborna\IntegrationsBundle\Event\AutobornaSyncFieldsLoadEvent;
use PHPUnit\Framework\TestCase;

class AutobornaSyncFieldsLoadEventTest extends TestCase
{
    public function testWorkflow(): void
    {
        $objectName = 'object';
        $fields     = [
            'fieldKey' => 'fieldName',
        ];

        $newFieldKey   = 'newFieldKey';
        $newFieldValue = 'newFieldValue';

        $event = new AutobornaSyncFieldsLoadEvent($objectName, $fields);
        $this->assertSame($objectName, $event->getObjectName());
        $this->assertSame($fields, $event->getFields());
        $event->addField($newFieldKey, $newFieldValue);
        $this->assertSame(
            array_merge($fields, [$newFieldKey => $newFieldValue]),
            $event->getFields()
        );
    }
}
