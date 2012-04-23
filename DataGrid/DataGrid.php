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

    protected $_actions;

    public function __construct($request, $router, $pager, array $config = array(), $title = '')
    {
        $this->_request   = $request;
        $this->_router    = $router;
        $this->_pager     = $pager;
        $this->_config    = $config;
        $this->_title     = $title;
        $this->_actions   = array();
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

    public function addAction($label, $route, $place = 'row', $confirm = null, $routeParams = array())
    {
        $this->_actions[$place][] = new Action($label, $route, $confirm, $routeParams);
        return $this;
    }

    public function hasActions($place = 'row')
    {
        if (!isset($this->_actions[$place])) {
            return false;
        }
        return count($this->_actions[$place]) > 0;
    }

    public function getActions($place = 'row')
    {
        if (!isset($this->_actions[$place])) {
            return array();
        }
        return $this->_actions[$place];
    }
}