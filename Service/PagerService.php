<?php

namespace Gloomy\PagerBundle\Service;

use Gloomy\PagerBundle\Pager\Pager;

class PagerService {

    private $_request;

    private $_router;

    public function __construct($request, $router)
    {
        $this->_request     = $request;
        $this->_router      = $router;
    }

    public function factory($items, $route = null, array $config = array(), array $addToURL = array())
    {
        return new Pager($this->_request, $this->_router, $items, $route, $config, $addToURL);
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
