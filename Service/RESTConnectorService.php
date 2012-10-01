<?php

namespace Gloomy\PagerBundle\Service;

use Symfony\Component\HttpFoundation\Response;

use Gloomy\PagerBundle\RESTConnector\DataTables;
use Gloomy\PagerBundle\RESTConnector\jqGrid;
use Gloomy\PagerBundle\RESTConnector\SlickGrid;

use Gloomy\PagerBundle\Pager\Pager;

class RESTConnectorService {

    public function __construct($connectorType, $request, $pagerService)
    {
        $this->_connectorType = $connectorType;
        $this->_request       = $request;
        $this->_pagerService  = $pagerService;
    }

    public function factory($pager)
    {
        if (!$pager instanceof Pager) {
            $pager = $this->_pagerService->factory($pager);
        }

        switch ($this->_connectorType) {

            case "DataTables":
                return new DataTables($this->_request, $pager);
                break;

            case "jqGrid":
                return new jqGrid($this->_request, $pager);
                break;

            case "SlickGrid":
                return new SlickGrid($this->_request, $pager);
                break;

            default:
                throw new \Exception('Unknown connector type');
                break;
        }
    }
}
