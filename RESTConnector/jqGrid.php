<?php

namespace Gloomy\PagerBundle\RESTConnector;

class jqGrid extends RESTBase
{
    public function __construct($request, $pager, array $config = array())
    {
        parent::__construct($request, $pager, $config);
    }

    public function handle()
    {
        // items per page
        $perPage = $this->_request->get('rows', 10);
        if ($perPage > 500) {
            $perPage = 500;
        }
        elseif ($perPage <= 0) {
            $perPage = 10;
        }

        // page number
        $pageNumber = $this->_request->get('page', 1);

        /**
         * Pager Configuration
         */
        $this->_pager->setCurrentPageNumber($pageNumber);
        $this->_pager->setItemCountPerPage($perPage);
        $fields = $this->_pager->getFields();
        $fieldsIndex = array_keys($fields);

        // Sort
        $sort = $this->_request->get('sidx', '');
        if (isset($fields[$sort])) {
            $dir = ( $this->_request->get('sord', 'asc') == 'asc' ) ? 'ASC' : 'DESC';
            $this->_pager->getWrapper()->setOrderBy(array($sort => $dir));
        }

        // Filter
        $search = $this->_request->get('searchField', false);
        if ($search !== false && in_array($search, $fieldsIndex)) {
            $f = array($search);
            $v = array($this->_request->get('searchString', ''));
            $o = array($this->operatorConvert($this->_request->get('searchOper', 'contains')));
            $this->_pager->getWrapper()->setFilters($f, $v, $o);
        }

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
                "page" => $infos->current,
                "total" => $infos->last,
                "records" => $infos->totalItemCount,
                "rows" => $datas
                );

        return $this->jsonResponse($response);
    }

    protected function operatorConvert($operator)
    {
        switch ($operator) {

            case "eq":
                return "equals";

            case "ne":
                return "notEquals";

            case "nc":
                return "notContains";

            case "nu":
                return "null";

            case "nn":
                return "notNull";

            case "in":
                return "in";

            case "ni":
            case "bw":
            case "bn":
            case "ew":
            case "en":
                // TODO

            case "cn":
            default:
                return "contains";
        }
    }
}
