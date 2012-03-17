<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Zend\Paginator\Adapter\Null;

use Gloomy\PagerBundle\Pager\Wrapper;

class NullWrapper extends Null implements Wrapper
{
    public function setOrderBy(array $orderBy)
    {
        return $this;
    }

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array())
    {
        return $this;
    }
}
