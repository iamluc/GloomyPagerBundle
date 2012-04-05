<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper;

class NullWrapper implements Wrapper
{
    protected $_count = null;

    public function __construct($count = 0)
    {
        $this->_count = $count;
    }

    public function count()
    {
        return $this->_count;
    }

    public function getFields()
    {
        return array();
    }

    /**
     * From Zend\Paginator\Adapter\Null
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($offset >= $this->count()) {
            return array();
        }

        $remainItemCount  = $this->count() - $offset;
        $currentItemCount = $remainItemCount > $itemCountPerPage ? $itemCountPerPage : $remainItemCount;

        return array_fill(0, $currentItemCount, null);
    }

    public function setOrderBy(array $orderBy)
    {
        return $this;
    }

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array())
    {
        return $this;
    }
}
