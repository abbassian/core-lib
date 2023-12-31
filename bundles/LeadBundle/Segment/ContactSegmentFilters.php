<?php

namespace Autoborna\LeadBundle\Segment;

/**
 * Class ContactSegmentFilters is array object containing filters.
 */
class ContactSegmentFilters implements \Iterator, \Countable
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array|ContactSegmentFilter[]
     */
    private $contactSegmentFilters = [];

    /**
     * @return $this
     */
    public function addContactSegmentFilter(ContactSegmentFilter $contactSegmentFilter)
    {
        $this->contactSegmentFilters[] = $contactSegmentFilter;

        return $this;
    }

    /**
     * Return the current element.
     *
     * @see  http://php.net/manual/en/iterator.current.php
     *
     * @return ContactSegmentFilter
     */
    public function current()
    {
        return $this->contactSegmentFilters[$this->position];
    }

    /**
     * Move forward to next element.
     *
     * @see  http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element.
     *
     * @see  http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid.
     *
     * @see  http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->contactSegmentFilters[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see  http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Count elements of an object.
     *
     * @see  http://php.net/manual/en/countable.count.php
     *
     * @return int
     */
    public function count()
    {
        return count($this->contactSegmentFilters);
    }
}
