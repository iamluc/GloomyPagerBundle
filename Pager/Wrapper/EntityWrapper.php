<?php

namespace Gloomy\PagerBundle\Pager\Wrapper;

use Gloomy\PagerBundle\Pager\Wrapper\QueryBuilderWrapper;

class EntityWrapper extends QueryBuilderWrapper
{
    public function __construct($entityManager, $entity, $fields = array(), $config = array())
    {
        parent::__construct($entityManager->getRepository($entity)->createQueryBuilder('e'));
    }
}
