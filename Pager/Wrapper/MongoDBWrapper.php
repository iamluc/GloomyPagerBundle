<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper;
use Gloomy\PagerBundle\Pager\Field;

class MongoDBWrapper extends QueryBuilderWrapper implements Wrapper
{
    public function __construct($builder, $fields = array(), $config = array())
    {
        $this->_builder = $builder;
        $this->_fields  = $fields;
        $this->_config  = $config;

        if (empty($this->_fields)) {
            $this->populateFields();
        }
    }

    public function populateFields()
    {
        foreach ($this->_builder->getQuery()->getClass()->fieldMappings as $property => $infos) {
            if ($infos['type'] == 'many') {
                continue;
            }
            $this->addField(new Field($property, $infos['type'], null, $property));
        }
    }

    // TODO : fait la même requete que getItems !!!!
    public function count()
    {
        if (is_null($this->_count)) {
            $this->_count = $this->_builder
                ->getQuery()
                ->count();
        }

        return $this->_count;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $result = $this->_builder
            ->skip($offset)
            ->limit($itemCountPerPage)
            ->getQuery()
            ->execute();

        return $result;
    }

    public function getField($alias)
    {
//         if (!isset($this->_fields[$alias])) {
//             $alias = $this->_builder->getRootAlias().'.'.$alias; // if alias does not exist, try with default object alias
//         }
        if (!isset($this->_fields[$alias])) {
            throw new \Exception('Unknown alias '.$alias);
        }
        return $this->_fields[$alias];
    }

    public function setOrderBy(array $orderBy)
    {
        $this->_builder->sort($orderBy);
    }

    public function setFilters(array $fields, array $values, array $operators = array(), array $logical = array())
    {
        static $paramNum;

        if (empty($fields)) {
            return $this;
        }

        // If we modify the query, we must recount the items
        $this->_count    = null;

        $criteria       = array();
//         $expr           = $this->_builder->expr();

        foreach ($fields as $key => $alias) {

            $field      = $this->getField($alias);
            $qualifier  = $field->getQualifier();

            $operator   = array_key_exists($key, $operators) ? $operators[$key] : 'contains';

            $value      = array_key_exists($key, $values) ? $values[$key] : '';
            $value      = $field->formatInput($value);
            if ('date' === $field->getType() && $value) {
                $date   = \DateTime::createFromFormat($field->getDateFormat(), $value);
                if ($date) {
//                     $value = $date->format('Y-m-d');
                    $value = new \MongoDate(strtotime($date->format('Y-m-d 00:00:00')));

                    // FIXME
                    $operator = 'equals';
                }
            }

            $paramName  = 'param'.++$paramNum;

            if (is_string($value) && ! strlen($value) && ! in_array($operator, array("null", "notNull", "n", "nn"))) {
                $criteria[]    = null;
                continue;
            }

            $expr           = $this->_builder->expr();

            switch ( $operator ) {

                default:
                case "c":
                case "contains":
                    $criteria[]    = $expr->field($qualifier)->equals(new \MongoRegex('/.*'.$value.'.*/i'));
                    break;

                case "nc":
                case "notContains":
                    $criteria[]    = $expr->field($qualifier)->not(new \MongoRegex('/.*'.$value.'.*/i'));
                    break;

                case "e":
                case "equals";
                    $criteria[]    = $expr->field($qualifier)->equals($value);
                    break;

                case "ne":
                case "notEquals":
                    $criteria[]    = $expr->field($qualifier)->notEqual($value);
                    break;

                case "g":
                case "greater":
                    $criteria[]    = $expr->field($qualifier)->gt($value);
                    break;

                case "ge":
                case "greaterOrEquals":
                    $criteria[]    = $expr->field($qualifier)->gte($value);
                    break;

                case "l":
                case "less":
                    $criteria[]    = $expr->field($qualifier)->lt($value);
                    break;

                case "le":
                case "lessOrEquals":
                    $criteria[]    = $expr->field($qualifier)->lte($value);
                    break;

                case "n":
                case "null":
                    $criteria[]    = $expr->field($qualifier)->exists(false);
                    break;

                case "nn":
                case "notNull":
                    $criteria[]    = $expr->field($qualifier)->exists(true);
                    break;

//                 case "i":
//                 case "in":
//                     if ( ! is_array( $value ) ) {
//                         $value     = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $value, -1, PREG_SPLIT_NO_EMPTY);
//                     }
//                     $criteria[]    = $expr->in($qualifier, ':'.$paramName);
//                     $this->_builder->setParameter($paramName, $value);
//                     break;
            }
        }

        if (empty($logical)) {
            $logical           = self::$logicalAND;
        }
        else {
            rsort( $logical ); // Start with last layer
        }

        foreach ($logical as $layer) {

            $criteriumIndex    = 0;
            $concatCriteria    = array();

            foreach ($layer as $condition) {

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
                    $count     = count($criteria) - $criteriumIndex;
                }

                // Criteria for the operator
                $subCriteria   = array();
                foreach (array_slice($criteria, $criteriumIndex, $count) as $criterium) {
                    if ($criterium) {
                        $subCriteria[] = $criterium;
                    }
                }
                $criteriumIndex += $count;

                // Apply operator
                if (count($subCriteria) > 1) {
                    $cond = $this->_builder->expr();
                    foreach ($subCriteria as $cri) {
                        if (strtolower($operator) == 'or') {
                            $cond->addOr($cri);
                        }
                        else {
                            $cond->addAnd($cri);
                        }
                    }
                    $concatCriteria[] = $cond;
//                     $method    = (strtolower($operator) == 'or') ? 'addOr' : 'addAnd';
//                     $concatCriteria[] = ($subCriteria) ? call_user_func_array(array($this->_builder->expr(), $method), $subCriteria)
//                     : null;
                }
                else {
                    $concatCriteria[] = current($subCriteria);
                }

                // Complete array
                for ($count--; $count; $count--) {
                    $concatCriteria[] = null;
                }
            }
            $criteria    = $concatCriteria;
        }
// var_dump($criteria);
// exit;
        $criterium    = current($criteria);
        if ($criterium) {
            $this->_builder->addAnd($criterium);
        }

        return $this;
    }
}
