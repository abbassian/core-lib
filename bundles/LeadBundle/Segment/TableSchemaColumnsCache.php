<?php

namespace Autoborna\LeadBundle\Segment;

use Doctrine\ORM\EntityManager;

/**
 * Class TableSchemaColumnsCache.
 */
class TableSchemaColumnsCache
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $cache;

    /**
     * TableSchemaColumnsCache constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cache         = [];
    }

    /**
     * @param $tableName
     *
     * @return array|false
     */
    public function getColumns($tableName)
    {
        if (!isset($this->cache[$tableName])) {
            $columns                 = $this->entityManager->getConnection()->getSchemaManager()->listTableColumns($tableName);
            $this->cache[$tableName] = $columns ?: [];
        }

        return $this->cache[$tableName];
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->cache = [];

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentDatabaseName()
    {
        return $this->entityManager->getConnection()->getDatabase();
    }
}
