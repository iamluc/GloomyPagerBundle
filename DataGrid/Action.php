<?php

namespace Gloomy\PagerBundle\DataGrid;

class Action
{
    protected $_label;

    protected $_route;

    protected $_routeParams;

    protected $_confirm;

    protected $_image;

    public function __construct($label, $route, $routeParams = array(), $confirm = null, $image = null)
    {
        $this->setLabel($label);
        $this->setRoute($route);
        $this->setRouteParams($routeParams);
        $this->setConfirm($confirm);
        $this->setImage($image);
    }

    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function setRoute($route)
    {
        $this->_route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->_route;
    }

    public function setConfirm($confirm)
    {
        $this->_confirm = $confirm;
        return $this;
    }

    public function getConfirm()
    {
        return $this->_confirm;
    }

    public function setRouteParams($routeParams)
    {
        $this->_routeParams = $routeParams;
        return $this;
    }

    public function getRouteParams()
    {
        return $this->_routeParams;
    }

    public function setImage($image)
    {
        $this->_image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->_image;
    }
}