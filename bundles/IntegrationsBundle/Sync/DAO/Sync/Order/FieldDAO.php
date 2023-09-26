<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order;

use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;

class FieldDAO
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var NormalizedValueDAO
     */
    private $value;

    /**
     * @param string $name
     */
    public function __construct($name, NormalizedValueDAO $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getValue(): NormalizedValueDAO
    {
        return $this->value;
    }
}
