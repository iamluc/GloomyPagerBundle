<?php

namespace Gloomy\PagerBundle\Pager;

use \Countable;

interface Wrapper extends Countable
{
    public function getItems($offset, $itemCountPerPage); // like Zend\Paginator\Adapter, but we don't want hard dependency

    public function getFields();

    public function setOrderBy(array $orderBy);

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array());
}
