<?php

namespace Gloomy\PagerBundle\Paginator\Adapter;

use Zend\Paginator\Adapter;

use Doctrine\ORM\QueryBuilder;

class QueryBuilderAdapter implements Adapter
{
    protected $_builder = null;

    protected $_count = null;

    public function __construct(QueryBuilder $builder)
    {
        $this->_builder     = $builder;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        return $this->_builder->setFirstResult($offset)
                              ->setMaxResults($itemCountPerPage)
                              ->getQuery()
                              ->getResult();
    }

    public function count()
    {
        if (is_null($this->_count)) {
            $builder        = clone $this->_builder;
            $this->_count   = $builder->select('COUNT('.$this->getDefaultAlias().')')
                                      ->getQuery()
                                      ->getSingleScalarResult();
        }

        return $this->_count;
    }

    protected function getDefaultAlias()
    {
        $from           = current($this->_builder->getDQLPart('from'));
        return $from->getAlias();
    }
}
