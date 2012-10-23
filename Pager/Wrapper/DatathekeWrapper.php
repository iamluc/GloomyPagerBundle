<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper;
use Gloomy\PagerBundle\Pager\Field;

class DatathekeWrapper implements Wrapper
{
    protected $_url  = null;
    protected $_config = null;

    protected $_orderBy = null;
    protected $_filters = null;

    protected $_count = null;
    protected $_fields = null;

    public function __construct($url, $config = array())
    {
        $this->_url    = $url;
        $this->_config = $config;
    }

    public function count()
    {
        if (is_null($this->_count)) {
            $response = $this->query(array(
                    'action' => 'count',
                    'filters' => $this->_filters
                    ));
            $this->_count = isset($response['count']) ? $response['count'] : 0;
        }
        return $this->_count;
    }

    public function getFields()
    {
        if (is_null($this->_fields)) {
            $response = $this->query(array('action' => 'fields'));
            $fields = array();
            if (isset($response['fields']) && is_array($response['fields'])) {
                foreach ($response['fields'] as $alias => $field) {
                    $fields[$alias] = new Field($field['property'], $field['type'], $field['label'], $field['qualifier'], $field['options']);
                }
            }
            $this->_fields = $fields;
        }

        return $this->_fields;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $response = $this->query(array(
                'action' => 'items',
                'offset' => $offset,
                'itemCountPerPage' => $itemCountPerPage,
                'filters' => $this->_filters,
                'orderBy' => $this->_orderBy
                ));
        return isset($response['items']) ? $response['items'] : array();
    }

    public function setOrderBy(array $orderBy)
    {
        $this->_orderBy = $orderBy;
        return $this;
    }

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array())
    {
        $this->_count = null;

        $this->_filters[] = array(
                'f' => $fields,
                'v' => $values,
                'o' => $operators,
                'l' => $logical
                );
        return $this;
    }

    protected function query($params) {

        $url = $this->_url.'?'.http_build_query($params);

        try {
            $response = file_get_contents($url);
        }
        catch (\Exception $e) {
            $response = false;
        }

        if (false === $response) {
            return false;
        }

        $datas = json_decode($response, true);
        if (null === $datas || ! is_array($datas)) {
            return false;
        }

        return $datas;
    }
}
