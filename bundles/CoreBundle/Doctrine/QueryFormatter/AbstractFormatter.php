<?php

namespace Autoborna\CoreBundle\Doctrine\QueryFormatter;

use Doctrine\DBAL\Connection;

/**
 * Help generate SQL statements to format column data.
 *
 * Class AbstractFormat
 */
abstract class AbstractFormatter
{
    protected $db;
    protected $platform;
    protected $name;

    /**
     * @return AbstractFormatter
     */
    public static function createFormatter(Connection $db)
    {
        $name  = $db->getDatabasePlatform()->getName();
        $class = '\Autoborna\CoreBundle\Doctrine\QueryFormatter\\'.ucfirst($name).'Formatter';

        return new $class($db);
    }

    public function __construct(Connection $db)
    {
        $this->db       = $db;
        $this->platform = $this->db->getDatabasePlatform();
        $this->name     = $this->platform->getName();
    }

    /**
     * Format field to datetime.
     *
     * @param        $field
     * @param string $format
     *
     * @return mixed
     */
    abstract public function toDateTime($field, $format = 'Y-m-d H:i:s');

    /**
     * Format field to date.
     *
     * @param        $field
     * @param string $format
     *
     * @return mixed
     */
    abstract public function toDate($field, $format = 'Y-m-d');

    /**
     * Format field to time.
     *
     * @param        $field
     * @param string $format
     *
     * @return mixed
     */
    abstract public function toTime($field, $format = 'H:i:s');

    /**
     * Format field to a numeric.
     *
     * @param $field
     *
     * @return mixed
     */
    abstract public function toNumeric($field);
}
