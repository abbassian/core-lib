<?php

namespace Autoborna\LeadBundle\Segment\Decorator;

use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;

class DateDecorator extends CustomMappedDecorator
{
    /**
     * @throws \Exception
     */
    public function getParameterValue(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        throw new \Exception('Instance of Date option needs to implement this function');
    }
}
