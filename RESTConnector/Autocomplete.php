<?php

namespace Gloomy\PagerBundle\RESTConnector;

use Symfony\Component\HttpFoundation\Response;

class Autocomplete
{
    public function __construct($request, $pager, array $config = array())
    {
        $this->_request = $request;
        $this->_pager   = $pager;

        $defaultConfig  = array(
                'return' => array('value', 'label'),
                'mapping' => array(
                        'value' => array('label', 'name', 'value', 'id'),    // try in this order
                        'label' => array('label', 'name', 'value', 'id')     // try in this order
                        )
                );
        $this->_config = array_merge($defaultConfig, $config);
    }

    public function handle()
    {
        // items per page
        $perPage = 20;

        /**
         * Pager Configuration
         */
        $fields = $this->_pager->getFields();
        $fieldsIndex = array_keys($fields);

        // Filter
        $search = $this->_request->get('term', false);
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

        /**
         * Formatting datas
         */
        $items = $this->_pager->getItems();
        $infos = $this->_pager->getPages();

        $datas = array();
        foreach ($items as $obj) {
            $item = array();

            if ($this->_config['return']) {
                foreach ($this->_config['return'] as $key) {

                    if (isset($this->_config['mapping'][$key])) {
                        $testFields = $this->_config['mapping'][$key];
                    }
                    else {
                        $testFields = $key;
                    }

                    if (!is_array($testFields)) {
                        $testFields = array($testFields);
                    }
                    foreach ($testFields as $name) {
                        if (isset($fields[$name])) {
                            $item[$key] = $fields[$name]->readData($obj);
                            break;
                        }
                    }
                }
            }
            else {
                foreach ($fields as $field) {
                    $item[$field->getProperty()] = $field->readData($obj);
                }
            }

            $datas[] = $item;
        }

        $response = $datas;

        return new Response(json_encode($response));
    }
}
