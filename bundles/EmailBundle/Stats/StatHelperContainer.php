<?php

namespace Autoborna\EmailBundle\Stats;

use Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException;
use Autoborna\EmailBundle\Stats\Helper\StatHelperInterface;

class StatHelperContainer
{
    private $helpers = [];

    public function addHelper(StatHelperInterface $helper)
    {
        $this->helpers[$helper->getName()] = $helper;
    }

    /**
     * @param $name
     *
     * @return StatHelperInterface
     *
     * @throws InvalidStatHelperException
     */
    public function getHelper($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new InvalidStatHelperException($name.' has not been registered');
        }

        return $this->helpers[$name];
    }
}
