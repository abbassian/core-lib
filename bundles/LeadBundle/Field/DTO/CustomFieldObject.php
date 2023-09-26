<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Field\DTO;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Exception\InvalidObjectTypeException;

class CustomFieldObject
{
    /**
     * @var array
     */
    private $objects = [
        'lead'    => 'leads',
        'company' => 'companies',
    ];

    /**
     * @var LeadField
     */
    private $leadField;

    /**
     * @throws InvalidObjectTypeException
     */
    public function __construct(LeadField $leadField)
    {
        $leadFieldObject = $leadField->getObject();
        if (!isset($this->objects[$leadFieldObject])) {
            throw new InvalidObjectTypeException($leadFieldObject.' has no associated object.');
        }

        $this->leadField = $leadField;
    }

    public function getObject(): string
    {
        return $this->objects[$this->leadField->getObject()];
    }
}
