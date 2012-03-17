<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper;
use Gloomy\PagerBundle\Paginator\Adapter\QueryBuilderAdapter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

class QueryBuilderWrapper extends QueryBuilderAdapter implements Wrapper
{
    static public $logicalAND   = array(array(array('o' => 'and')));
    static public $logicalOR    = array(array(array('o' => 'or')));

    protected $_fields = null;
    protected $_config = null;

    public function __construct(QueryBuilder $builder, $fields = array(), $config = array())
    {
        parent::__construct($builder);

        $this->_fields = $fields;
        $this->_config = $config;
    }

    public function setOrderBy(array $orderBy)
    {
        $first    = true;
        foreach ($orderBy as $alias => $order) {

            $sort    = $this->getFieldSql($alias);

            if ($first) {
                $this->_builder->orderBy($sort, $order);
                $first = false;
            }
            else {
                $this->_builder->addOrderBy($sort, $order);
            }
        }

        return $this;
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
        $expr           = $this->_builder->expr();

        foreach ($fields as $key => $alias) {

            $field      = $this->getFieldSql($alias);
            $value      = array_key_exists($key, $values) ? $values[$key] : '';
            $operator   = array_key_exists($key, $operators) ? $operators[$key] : 'contains';
            $paramName  = 'param'.++$paramNum;

            if (is_string($value) && ! strlen($value) && ! in_array($operator, array("null", "notNull", "n", "nn"))) {
                $criteria[]    = null;
                continue;
            }

            switch ( $operator ) {

                default:
                case "c":
                case "contains":
                    $criteria[]    = $expr->like($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, '%'.$value.'%');
                    break;

                case "nc":
                case "notContains":
                    $criteria[]    = $expr->not($expr->like($field, ':'.$paramName));
                    $this->_builder->setParameter($paramName, '%'.$value.'%');
                    break;

                case "e":
                case "equals":
                    $criteria[]    = $expr->eq($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "ne":
                case "notEquals":
                    $criteria[]    = $expr->neq($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "g":
                case "greater":
                    $criteria[]    = $expr->gt($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "ge":
                case "greaterOrEquals":
                    $criteria[]    = $expr->gte($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "l":
                case "less":
                    $criteria[]    = $expr->lt($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "le":
                case "lessOrEquals":
                    $criteria[]    = $expr->lte($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;

                case "n":
                case "null":
                    $criteria[]    = $expr->isNull($field);
                    break;

                case "nn":
                case "notNull":
                    $criteria[]    = $expr->isNotNull($field);
                    break;

                case "i":
                case "in":
                    if ( ! is_array( $value ) ) {
                        $value     = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $value, -1, PREG_SPLIT_NO_EMPTY);
                    }
                    $criteria[]    = $expr->in($field, ':'.$paramName);
                    $this->_builder->setParameter($paramName, $value);
                    break;
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
                    $method    = (strtolower($operator) == 'or') ? 'orX' : 'andX';
                    $concatCriteria[] = ($subCriteria) ? call_user_func_array(array($this->_builder->expr(), $method), $subCriteria)
                                                       : null;
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

        $criterium    = current($criteria);
        if ($criterium) {
            $this->_builder->andWhere($criterium);
        }

        return $this;
    }

    protected function getFieldSql($alias)
    {
        if (array_key_exists($alias, $this->_fields)) {
            return $this->_fields[$alias]['field'];
        }
        else if (strpos($alias, '.') !== false) {
            return $alias;
        }
        else {
            return $this->getDefaultAlias().'.'.$alias;
        }
    }
}
