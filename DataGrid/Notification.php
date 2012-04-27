<?php

namespace Gloomy\PagerBundle\DataGrid;

class Notification
{
    protected $_message;

    protected $_type;

    public function __construct($message, $type = 'information')
    {
        $this->_message = $message;
        $this->_type    = $type;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function getType()
    {
        return $this->_type;
    }
}