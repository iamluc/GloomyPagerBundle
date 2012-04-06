<?php

namespace Gloomy\PagerBundle\DataGrid;

use Gloomy\PagerBundle\Pager\Wrapper;

class DataGrid
{
    protected $_request;

    protected $_router;

    protected $_pager;

    protected $_config;

    protected $_title;

    public function __construct($request, $router, $pager, array $config = array(), $title = '')
    {
        $this->_request   = $request;
        $this->_router    = $router;
        $this->_pager     = $pager;
        $this->_config    = $config;
        $this->_title     = $title;
    }

    public function getPager()
    {
        return $this->_pager;
    }

    public function getFields($all = false)
    {
        $fields = array();
        foreach ($this->getPager()->getFields() as $alias => $field) {
            if ($all || $field->isVisible()) {
                $fields[$alias] = $field;
            }
        }
        return $fields;
    }

    public function getItems()
    {
        return $this->getPager()->getItems();
    }

    public function path($page = null, array $parameters = array())
    {
        return $this->getPager()->path($page, $parameters);
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }
}