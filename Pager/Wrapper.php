<?php

namespace Gloomy\PagerBundle\Pager;

use Zend\Paginator\Adapter;

interface Wrapper extends Adapter
{
    public function setOrderBy(array $orderBy);

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array());
}