<?php

namespace Gloomy\PagerBundle\RESTConnector;

class Datatheke extends RESTBase
{
    public function __construct($request, $pager, array $config = array())
    {
        parent::__construct($request, $pager, $config);
    }

    public function handle()
    {
        $wrapper = $this->_pager->getWrapper();

        // set filters
        foreach ($this->_request->get('filters', array()) as $filter) {
            $wrapper->setFilters(
                    isset($filter['f']) ? $filter['f'] : array(),
                    isset($filter['v']) ? $filter['v'] : array(),
                    isset($filter['o']) ? $filter['o'] : array(),
                    isset($filter['l']) ? $filter['l'] : array()
            );
        }

        // set order by
        $wrapper->setOrderBy($this->_request->get('orderBy', array()));

        // Response
        switch ($this->_request->get('action', null)) {

            default:
            case 'count':
                return $this->jsonResponse(array('count' => (int) $wrapper->count()));
                break;

            case 'fields':
                $fields = array();
                foreach ($wrapper->getFields() as $alias => $field) {
                    $fields[$alias] = array(
                            'property' => $field->getProperty(),
                            'type' => $field->getType(),
                            'label' => $field->getLabel(),
                            'qualifier' => $field->getQualifier(),
                            'options' => $field->getOptions()
                            );
                }
                return $this->jsonResponse(array('fields' => $fields));
                break;

            case 'items':
                $items = array();
                foreach ($wrapper->getItems($this->_request->get('offset', 0), $this->_request->get('itemCountPerPage', 0)) as $item) {
                    $tmp = array();
                    foreach ($wrapper->getFields() as $alias => $field) {
                        $tmp[$field->getProperty()] = $field->readData($item);
                    }
                    $items[] = $tmp;
                }
                return $this->jsonResponse(array('items' => $items, 'debug' => $this->_request->get('filters', array())));
                break;
        }
    }
}
