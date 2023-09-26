<?php

namespace Autoborna\PointBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\PointBundle\Entity\Point;

class PointEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Point &$point, $isNew = false)
    {
        $this->entity = &$point;
        $this->isNew  = $isNew;
    }

    /**
     * @return Point
     */
    public function getPoint()
    {
        return $this->entity;
    }

    public function setPoint(Point $point)
    {
        $this->entity = $point;
    }
}
