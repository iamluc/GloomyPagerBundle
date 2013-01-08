<?php

namespace Gloomy\PagerBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder as DbalQB;

use Gloomy\PagerBundle\Pager\Pager;

use Gloomy\PagerBundle\Pager\Wrapper;
use Gloomy\PagerBundle\Pager\Wrapper\ArrayWrapper;
use Gloomy\PagerBundle\Pager\Wrapper\NullWrapper;
use Gloomy\PagerBundle\Pager\Wrapper\QueryBuilderWrapper;
use Gloomy\PagerBundle\Pager\Wrapper\EntityWrapper;

class PagerService {

    private $_request;

    private $_router;

    private $_entityManager;

    public function __construct($request, $router, $entityManager)
    {
        $this->_request       = $request;
        $this->_router        = $router;
        $this->_entityManager = $entityManager;
    }

    public function factory($wrapper, $route = null, array $config = array(), array $addToURL = array())
    {
        if (!$wrapper instanceof Wrapper) {
            if ($wrapper instanceof QueryBuilder) {
                $wrapper    = new QueryBuilderWrapper($wrapper);
            }
            elseif (is_array($wrapper)) {
                $wrapper    = new ArrayWrapper($wrapper);
            }
            elseif (is_string($wrapper)) {
                $wrapper    = new EntityWrapper($this->_entityManager, $wrapper);
            }
            else {
                $wrapper    = new NullWrapper((int) $wrapper);
            }
        }

        if (is_null($route)) {
            $route = $this->_request->get('_route');
        }

        return new Pager($this->_request, $this->_router, $wrapper, $route, $config, $addToURL);
    }

    public function decodeFilters($filters)
    {
        $fields     = array();
        $values     = array();
        $operators  = array();
        $logical    = array();

        $this->_readLayer(array('criteria'=>array($filters)), 0, $fields, $values, $operators, $logical);
        ksort($logical);

        return array($fields, $values, $operators, $logical);
    }

    protected function _readLayer($layer, $layerNum, &$fields, &$values, &$operators, &$logical)
    {
        $last           = 0;
        $criteriumIndex = 0;

        $hasSubLayer    = false;
        foreach ($layer['criteria'] as $criterium) {
            if (in_array($criterium['operator'], array( 'and', 'or' ))) {
                $hasSubLayer = true;
            }
        }

        foreach ($layer['criteria'] as $criterium) {

            if (in_array($criterium['operator'], array( 'and', 'or' ))) {
                // next layer
                $criteriumIndex += $this->_readLayer($criterium, $layerNum + 1, $fields, $values, $operators, $logical);

                // condition
                $logical[$layerNum][] = array(
                        'o' => $criterium['operator'],
                        'c' => $criteriumIndex - $last
                );
            }
            else {

                if ($hasSubLayer) {
                    // next layer
                    $criteriumIndex += $this->_readLayer(array('operator' => 'or', 'criteria' => array($criterium)), $layerNum + 1, $fields, $values, $operators, $logical);

                    // condition
                    $logical[$layerNum][] = array(
                            'o' => 'or',
                            'c' => $criteriumIndex - $last
                    );
                }
                else {
                    // field
                    $fields[]    = $criterium['field'];
                    $operators[] = $criterium['operator'];
                    $values[]    = $criterium['value'];
                    $criteriumIndex++;

                    // condition
                    $logical[$layerNum][] = array(
                            'o' => 'or',
                            'c' => 1
                    );
                }
            }
            $last    = $criteriumIndex;
        }

        ksort($logical[$layerNum]);
        return $criteriumIndex;
    }
}
