<?php

namespace Gloomy\PagerBundle\Pager;

use Gloomy\PagerBundle\Pager\Wrapper;

class Pager
{
    /**
     * Paginator
     */
    protected $_paginator;

    /**
     * Wrapper
     */
    protected $_wrapper;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $_request;

    /**
     * Config
     */
    protected $_config;

    /**
     * Router
     */
    protected $_router;

    /**
     * Route
     */
    protected $_route;

    /**
     * Datas to append to URL
     */
    protected $_addToURL;

    public function __construct($request, $router, Wrapper $wrapper, $route = null, array $config = array(), array $addToURL = array())
    {
        /**
         * Initialize pager
         */
        static $pagerNum;

        $pagerVar           = isset($config['pagerVar']) ? $config['pagerVar'] : '_gp'.$pagerNum++;
        $defaultConfig      = array(
                'pagerVar'              => $pagerVar,
                'pageVar'               => $pagerVar.'[p]',
                'orderByVar'            => $pagerVar.'[o]',
                'filtersVar'            => $pagerVar.'[f]',
                'itemsPerPageVar'       => $pagerVar.'[pp]',
                'pageRange'             => 5,
                'itemsPerPage'          => 10,
                'itemsPerPageChoices'   => array(10, 20, 100, 500, 1000)
        );
        $this->_config      = array_merge($defaultConfig, $config);
        if (! in_array($this->_config['itemsPerPage'], $this->_config['itemsPerPageChoices'])) {
            $this->_config['itemsPerPageChoices'][]    = $this->_config['itemsPerPage'];
            sort( $this->_config['itemsPerPageChoices'] );
        }

        $this->_request     = $request;
        $this->_router      = $router;
        $this->_route       = $route;
        $this->_addToURL    = $addToURL;
        $this->_wrapper     = $wrapper;
    }

    protected function getPaginator()
    {
        if (is_null($this->_paginator)) {
            $this->initializePaginator();
        }
        return $this->_paginator;
    }

    protected function initializePaginator()
    {
        /**
         * Sorting & filtering
         */
        $orderBy            = $this->getValue('orderByVar');
        if (is_array($orderBy)) {
            $this->_wrapper->setOrderBy($orderBy);
        }

        $filters            = $this->getValue('filtersVar');
        if (is_array($filters)) {
            $this->_wrapper->setFilters(
                    isset($filters['f']) ? $filters['f'] : array(), // Alias of the field
                    isset($filters['v']) ? $filters['v'] : array(), // Value
                    isset($filters['o']) ? $filters['o'] : array(), // Operator
                    isset($filters['l']) ? $filters['l'] : array()  // Logical (AND/OR)
            );
        }

        /**
         * Create Paginator
         *
         * Note : Could be a Zend\Paginator\Paginator if the wrappers implements Zend\Paginator\Adapter
         * But to avoid an hard dependy to Zend Framework, we use our own compatible class
         * (1 file of 200 lines instead of 20 Mo package)
         *
         */
        $this->_paginator   = new Paginator($this->_wrapper);
        $this->_paginator->setCurrentPageNumber((int) $this->getValue('pageVar', 1));
        $this->_paginator->setItemCountPerPage((int) $this->getValue('itemsPerPageVar', $this->getConfig('itemsPerPage')));
        $this->_paginator->setPageRange((int) $this->getConfig('pageRange'));
    }

    public function getWrapper()
    {
        return $this->_wrapper;
    }

    public function getConfig($option)
    {
        return $this->_config[$option];
    }

    public function getValue($option, $default = null)
    {
        return $this->_request->get($this->getConfig($option), $default, true);
    }

    public function getOrderBy()
    {
        return $this->getValue('orderByVar', array());
    }

    public function getFilters()
    {
        return $this->getValue('filtersVar', array());
    }

    public function getCurrentPageNumber()
    {
        return $this->getPaginator()->getCurrentPageNumber();
    }

    public function setCurrentPageNumber($page)
    {
        $this->getPaginator()->setCurrentPageNumber($page);
        return $this;
    }

    public function setItemCountPerPage($itemCount)
    {
        $this->getPaginator()->setItemCountPerPage($itemCount);
        return $this;
    }

    public function getFields()
    {
        return $this->_wrapper->getFields();
    }

    public function getItems()
    {
        return $this->getPaginator()->getCurrentItems();
    }

    public function getPages()
    {
        return $this->getPaginator()->getPages();
    }

    public function setItemsPerPage($num)
    {
        $this->_config['itemsPerPage'] = $num;
    }

    public function getItemsPerPage()
    {
        return $this->getValue('itemsPerPageVar', $this->getConfig('itemsPerPage'));
    }

    public function path($page = null, array $parameters = array(), $route = null)
    {
        $default            = array(    $this->getConfig('pageVar')         => $page ? (int) $page : $this->getCurrentPageNumber(),
                                        $this->getConfig('orderByVar')      => $this->getValue('orderByVar'),
                                        $this->getConfig('filtersVar')      => $this->getValue('filtersVar'),
                                        $this->getConfig('itemsPerPageVar') => $this->getValue('itemsPerPageVar')
                                        );

        $parameters         = array_merge($this->_addToURL, $default, $parameters);
        $route              = ($route) ? $route : $this->_route;
        return $this->_router->generate($route, $parameters, false);
    }

    public function pathOrderBy($orderBy, array $parameters = array(), $page = null, $route = null)
    {
        $default            = array(    $this->getConfig('pageVar')         => $page ? (int) $page : $this->getCurrentPageNumber(),
                                        $this->getConfig('orderByVar')      => $orderBy,
                                        $this->getConfig('filtersVar')      => $this->getValue('filtersVar'),
                                        $this->getConfig('itemsPerPageVar') => $this->getValue('itemsPerPageVar')
                                        );

        $parameters         = array_merge($this->_addToURL, $default, $parameters);
        $route              = ($route) ? $route : $this->_route;
        return $this->_router->generate($route, $parameters, false);
    }

    public function pathForm($page = null, array $parameters = array(), $route = null)
    {
        $default            = array(    $this->getConfig('pageVar')         => $page ? (int) $page : $this->getCurrentPageNumber(),
                                        $this->getConfig('orderByVar')      => $this->getValue('orderByVar'),
                                        );

        $parameters         = array_merge($this->_addToURL, $default, $parameters);
        $route              = ($route) ? $route : $this->_route;
        return $this->_router->generate($route, $parameters, false);
    }
}
