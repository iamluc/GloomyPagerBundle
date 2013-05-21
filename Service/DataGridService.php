<?php

namespace Gloomy\PagerBundle\Service;

use Gloomy\PagerBundle\DataGrid\DataGrid;
use Gloomy\PagerBundle\Pager\Pager;

class DataGridService {

    private $_request;

    private $_router;

    private $_pager;

    public function __construct($request, $router, $pager)
    {
        $this->_request = $request;
        $this->_router  = $router;
        $this->_pager   = $pager;
    }

    public function factory($pager, array $config = array(), $title = '')
    {
        if (!$pager instanceof Pager) {
            $pagerConfig = isset($config['pagerConfig']) ? $config['pagerConfig'] : array();
            $addToURL    = isset($config['addToURL']) ? $config['addToURL'] : array();
            $route       = isset($config['route']) ? $config['route'] : null;
            $pager       = $this->_pager->factory($pager, $route, $pagerConfig, $addToURL);
        }
        return new DataGrid($this->_request, $this->_router, $pager, $config, $title);
    }
}
