<?php

namespace Gloomy\PagerBundle\RESTConnector;

class DataTables extends RESTBase
{
    public function __construct($request, $pager, array $config = array())
    {
        parent::__construct($request, $pager, $config);
    }

    public function handle()
    {
        // items per page
        $perPage = $this->_request->get('iDisplayLength', 10);
        if ($perPage > 500) {
            $perPage = 500;
        }
        elseif ($perPage <= 0) {
            $perPage = 10;
        }

        // page number
        $pageNumber = ($this->_request->get('iDisplayStart', 0) / $perPage) + 1;

        // Number of columns to display
        $nbColumns = $this->_request->get('iColumns', 1);

        /**
         * Pager Configuration
         */
        $this->_pager->setCurrentPageNumber($pageNumber);
        $this->_pager->setItemCountPerPage($perPage);
        $fields = $this->_pager->getFields();
        $fieldsIndex = array_keys($fields);

        // Sort
        $nbSort = $this->_request->get('iSortingCols', 0);
        $sorts = array();
        for ($i = 0; $i < $nbSort && $i < 10; $i++) {
            $colIndex = $this->_request->get('iSortCol_'.$i, false);
            if ($colIndex !== false) {
                $col = $fieldsIndex[$colIndex];
                $dir = $this->_request->get('sSortDir_'.$i, 'asc');

                $sorts[$col] = $dir;
            }
        }
        if ($sorts) {
            $this->_pager->getWrapper()->setOrderBy($sorts);
        }

        // Filter
        $search = $this->_request->get('sSearch', false);
        if ($search !== false) {
            $f = array();
            $v = array();
            $o = array();
            foreach ($fieldsIndex as $key) {
                $f[] = $key;
                $v[] = $search;
                $o[] = "contains";
            }
            $this->_pager->getWrapper()->setFilters($f, $v, $o, array(array(array('o' => 'or'))));
        }

        // TODO: individual column filtering

        /**
         * Formatting datas
         */
        $items = $this->_pager->getItems();
        $infos = $this->_pager->getPages();

        $datas = array();
        $columns = array();
        foreach ($items as $obj) {
            $item = array();
            $cpt = 1;
            foreach ($fields as $field) {
                if (!$field->isVisible()) {
                    continue;
                }

                $columns[$field->getProperty()] = $field->getLabel();

                $item[] = (string) $field->readData($obj);
                if ($cpt++ >= $nbColumns) {
                    break;
                }
            }
            $datas[] = $item;
        }

        $response = array(
                "sEcho" => $this->_request->get('sEcho', 1),
                "iTotalRecords" => $infos->totalItemCount,
                "iTotalDisplayRecords" => $infos->totalItemCount,
                "columns" => $columns, // debug
                "aaData" => $datas
                );

        return $this->jsonResponse($response);
    }
}
