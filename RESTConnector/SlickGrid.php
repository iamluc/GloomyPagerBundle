<?php

namespace Gloomy\PagerBundle\RESTConnector;

class SlickGrid extends RESTBase
{
    public function __construct($request, $pager, array $config = array())
    {
        parent::__construct($request, $pager, $config);
    }

    public function handle()
    {
        // items per page
        $perPage = $this->_request->get('count', 10);
        if ($perPage > 500) {
            $perPage = 500;
        }
        elseif ($perPage <= 0) {
            $perPage = 10;
        }

        // page number
        $pageNumber = ($this->_request->get('offset', 0) / $perPage) + 1;

        /**
         * Pager Configuration
         */
        $this->_pager->setCurrentPageNumber($pageNumber);
        $this->_pager->setItemCountPerPage($perPage);
        $fields = $this->_pager->getFields();
        $fieldsIndex = array_keys($fields);

        /**
         * Formatting datas
         */
        $items = $this->_pager->getItems();
        $infos = $this->_pager->getPages();

        $datas = array();
        foreach ($items as $obj) {
            $item = array();
            foreach ($fields as $field) {
                $item[$field->getProperty()] = $field->readData($obj);
            }
            $datas[] = $item;
        }

        $response = array(
                "fromPage" => $this->_request->get('fromPage', 0),
                "total" => $infos->totalItemCount,
                "count" => count($datas),
                "stories" => $datas
                );

        return $this->jsonResponse($response);
    }
}
