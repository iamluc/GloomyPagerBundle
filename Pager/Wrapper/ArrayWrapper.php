<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper;
use Gloomy\PagerBundle\Pager\Field;

class ArrayWrapper implements Wrapper
{
    static public $logicalAND   = array(array(array('o' => 'and')));
    static public $logicalOR    = array(array(array('o' => 'or')));

    protected $_array  = null;
    protected $_count  = null;
    protected $_fields = null;
    protected $_config = null;

    public function __construct(array $array, $fields = array(), $config = array())
    {
        $this->_array  = $array;
        $this->_count  = count($array);
        $this->_fields = $fields;
        $this->_config = $config;

        if (empty($this->fields)) {
            $this->populateFields();
        }
    }

    protected function populateFields()
    {
        if (empty($this->_array)) {
            return;
        }

        foreach (current($this->_array) as $key => $val) {
            $this->_fields[$key] = new Field($key);
        }
    }

    public function count()
    {
        return $this->_count;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        return array_slice($this->_array, $offset, $itemCountPerPage);
    }

    public function setOrderBy(array $orderBy)
    {
        $sortArray          = array();
        foreach ($orderBy as $field => $order) {

            $colArray       = array();
            foreach ($this->_array as $key => $row) {
                $colArray[$key] = $row[$field];
            }
            $sortArray[]    = $colArray;
            $sortArray[]    = (strtolower($order) == 'asc') ? SORT_ASC : SORT_DESC;
        }

        $sortArray[]        = & $this->_array;
        call_user_func_array('array_multisort', $sortArray);

        return $this;
    }

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array())
    {
        if (empty($fields)) {
            return $this;
        }

        if (empty($logical)) {
            $logical           = self::$logicalAND;
        }
        else {
            rsort( $logical ); // Start with last layer
        }

        foreach ($this->_array as $key => $item) {
            if (! $this->checkItem($item, $fields, $values, $operators, $logical)) {
                unset( $this->_array[$key] );
            }
        }

        $this->_count = count($this->_array);

        return $this;
    }

    protected function checkItem($row, $fields, $values, $operators, $logical)
    {
        $criteria    = array();
        foreach ($fields as $key => $alias) {

            $field           = $this->getField($alias);

            $itemValue       = $row[$alias];
            $filterOperator  = array_key_exists($key, $operators) ? $operators[$key] : 'contains';
            $filterValue     = array_key_exists($key, $values) ? $values[$key] : '';
            $filterValue     = $field->formatInput($filterValue);
            $criteria[]      = $this->checkCondition($itemValue, $filterOperator, $filterValue);
        }

        foreach ( $logical as $layer ) {

            $criteriumIndex    = 0;
            $concatCriteria    = array();

            foreach ( $layer as $condition ) {

                if (array_key_exists('operator', $condition)) {
                    $operator  = $condition['operator'];
                }
                elseif (array_key_exists('o', $condition)) {
                    $operator  = $condition['o'];
                }
                else {
                    $operator  = 'and';
                }

                if (array_key_exists('count', $condition)) {
                    $count     = $condition['count'];
                }
                elseif (array_key_exists('c', $condition)) {
                    $count     = $condition['c'];
                }
                else {
                    $count     = count( $criteria ) - $criteriumIndex;
                }

                // Criteria for the operator
                $subCriteria   = array();
                foreach (array_slice($criteria, $criteriumIndex, $count, true) as $criterium) {
                    if (! is_null($criterium)) {
                        $subCriteria[] = $criterium;
                    }
                }
                $criteriumIndex += $count;

                // Apply operator
                if (strtolower($operator) == 'or') {
                    $concatCriteria[]    = in_array(true, $subCriteria, true);
                }
                else {
                    $concatCriteria[]    = ! in_array(false, $subCriteria, true);
                }

                // Complete array
                for ($count--; $count; $count--) {
                    $concatCriteria[] = null;
                }
            }
            $criteria    = $concatCriteria;
        }

        return current($criteria);
    }

    protected function checkCondition($itemValue, $operator, $filterValue)
    {
        $keep        = false;

        if (is_string($filterValue) && ! strlen($filterValue) && ! in_array($operator, array("null", "notNull", "n", "nn"))) {
            return null;
        }

        switch ( $operator ) {

            default:
            case "c":
            case "contains":
                $keep    = ( preg_match( "/".preg_quote( $filterValue, "/" )."/i", $itemValue ) );
                break;

            case "nc":
            case "notContains":
                $keep    = ! ( preg_match( "/".preg_quote( $filterValue, "/" )."/i", $itemValue ) );
                break;

            case "e":
            case "equals":
                $keep    = ( preg_match( "/^".preg_quote( $filterValue, "/" )."\$/i", $itemValue ) );
                break;

            case "ne":
            case "notEquals":
                $keep    = ! ( preg_match( "/^".preg_quote( $filterValue, "/" )."\$/i", $itemValue ) );
                break;

            case "li":
            case "like":
                $keep    = ( preg_match( "/^".str_replace( "*", ".*", preg_quote( $filterValue, "/" ) )."\$/i", $itemValue ) );
                break;

            case "g":
            case "greater":
                $keep    = ( $itemValue > $filterValue );
                break;

            case "ge":
            case "greaterOrEquals":
                $keep    = ( $itemValue >= $filterValue );
            	break;

            case "l":
            case "less":
                $keep    = ( $itemValue < $filterValue );
                break;

            case "le":
            case "lessOrEquals":
                $keep    = ( $itemValue <= $filterValue );
                break;

            case "n":
            case "null":
                $keep    = ( is_null( $itemValue ) || $itemValue === false || $itemValue === "" );
                break;

            case "nn":
            case "notNull":
                $keep    = ! ( is_null( $itemValue ) || $itemValue === false || $itemValue === "" );
                break;

            case "i":
            case "in":
                if ( ! is_array( $filterValue ) ) {
                    $filterValue    = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $filterValue, -1, PREG_SPLIT_NO_EMPTY);
                }
                $keep    = (in_array( $itemValue, $filterValue ));
                break;
        }

        return (bool) $keep;
    }

    protected function getField($alias)
    {
        if (array_key_exists($alias, $this->_fields)) {
            return $this->_fields[$alias];
        }
        throw new \Exception('Unknown alias '.$alias);
    }
}
