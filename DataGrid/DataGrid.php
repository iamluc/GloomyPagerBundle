<?php

namespace Gloomy\PagerBundle\DataGrid;

class DataGrid
{
    protected $_request;

    protected $_router;

    protected $_pager;

    protected $_config;

    protected $_title;

    protected $_actions;

    protected $_notifications;

    public function __construct($request, $router, $pager, array $config = array(), $title = '')
    {
        $this->_request       = $request;
        $this->_router        = $router;
        $this->_pager         = $pager;
        $this->_config        = array_merge(array('rowIdVar' => 'id'), $config);
        $this->_title         = $title;
        $this->_actions       = array();
        $this->_notifications = array();
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

    public function getField($alias)
    {
        $fields = $this->getPager()->getFields();
        return $fields[$alias];
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

    public function addAction(Action $action, $alias = null, $place = 'row')
    {
        if (is_null($alias)) {
            $alias = $action->getLabel();
        }
        $this->_actions[$place][$alias] = $action;
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

    public function getAction($alias, $place = 'row')
    {
        return $this->_actions[$place][$alias];
    }

    public function addNotification(Notification $notification)
    {
        $this->_notifications[] = $notification;
        return $this;
    }

    public function hasNotifications()
    {
        return count($this->_notifications) > 0;
    }

    public function getNotifications()
    {
        return $this->_notifications;
    }

    public function showOnly($fields)
    {
        foreach ($this->getFields(true) as $alias => $field) {
            if (in_array($alias, $fields)) {
                $field->show();
            }
            else {
                $field->hide();
            }
        }
        return $this;
    }

    public function getConfig($option)
    {
        return $this->_config[$option];
    }

    public function setItemsPerPage($num)
    {
        $this->getPager()->setItemsPerPage($num);
        return $this;
    }
}
