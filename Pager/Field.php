<?php

namespace Gloomy\PagerBundle\Pager;

class Field
{
    public static $defaultDateFormat = 'Y-m-d H:i:s';

    protected $_property;

    protected $_qualifier;

    protected $_type;

    protected $_label;

    protected $_options;

    protected $_dateFormat;

    protected $_visible;

    public function __construct($property, $type = 'text', $label = '', $qualifier = null, $options = array())
    {
        $this->_property  = $property;
        $this->_type      = $type;
        $this->_label     = $label ?: ucfirst($property);
        $this->_qualifier = $qualifier ?: $property;
        $this->_options   = $options;
        $this->_visible   = true;
    }

    public function getProperty()
    {
        return $this->_property;
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

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
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

    public function readData($item)
    {
        $accessors = array();
        if (isset($this->_options['tree']) && $this->_options['tree']) {
            $accessors = explode('.', $this->getProperty());
        }
        else {
            $accessors[] = $this->getProperty();
        }

        foreach ($accessors as $property) {
            if (is_null($item)) {
                return null;
            }
            elseif (is_array($item)) {
                $item = $item[$property];
            }
            else {
                if (method_exists($item, 'get'.ucfirst($property))) {
                    $item = call_user_func_array(array($item, 'get'.ucfirst($property)), array());
                }
                else if (method_exists($item, 'is'.ucfirst($property))) {
                    $item = call_user_func_array(array($item, 'is'.ucfirst($property)), array());
                }
                else if (method_exists($item, '__call')) {
                    $item = call_user_func_array(array($item, 'get'.ucfirst($property)), array());
                }
                else {
                    return null;
                }
            }
        }

        if ($item instanceOf \DateTime) {
            return $item->format($this->getDateFormat());
        }

        return $item;
    }
}
