<?php

namespace Gloomy\PagerBundle\Pager;

class Field
{
    public static $defaultDateFormat = 'Y-m-d H:i:s';

    protected $_name;

    protected $_qualifier;

    protected $_type;

    protected $_label;

    protected $_dateFormat;

    protected $_visible;

    public function __construct($name, $type = 'text', $label = '', $qualifier = null)
    {
        $this->_name      = $name;
        $this->_type      = $type;
        $this->_label     = $label ?: ucfirst($name);
        $this->_qualifier = $qualifier ?: $name;
        $this->_visible   = true;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getQualifier()
    {
        return $this->_qualifier;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
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

    public function setDateFormat($dateFormat)
    {
        $this->_dateFormat = $dateFormat;
        return $this;
    }

    public function getDateFormat()
    {
        if (is_null($this->_dateFormat)) {
            $this->_dateFormat = self::$defaultDateFormat;
        }
        return $this->_dateFormat;
    }

    public function show()
    {
        $this->_visible = true;
        return $this;
    }

    public function hide()
    {
        $this->_visible = false;
        return $this;
    }

    public function isVisible()
    {
        return $this->_visible;
    }
}
