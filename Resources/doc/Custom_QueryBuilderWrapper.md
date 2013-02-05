HOW TO : customize a QueryBuilderWrapper
========================================

PHP Controller
``` php
<?php

namespace Gloomy\ExemplesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Gloomy\PagerBundle\Pager\Wrapper\QueryBuilderWrapper;
use Gloomy\PagerBundle\Pager\Field;

class DefaultController extends Controller
{
    /**
     * @Route("/exemple3")
     * @Template()
     */
    public function exemple3Action()
    {
        // QueryBuilder
        $qb = $this->getDoctrine()->getRepository('GloomyExempleBundles:Person')->createQueryBuilder('s')
            ->addSelect('f')->leftJoin('s.father', 'f')
            ->addSelect('gf')->leftJoin('f.father', 'gf')
        ;

        // Wrapper
        $wrapper = new QueryBuilderWrapper($qb);
        $wrapper
            ->addField(new Field('father.name', 'string', 'Father', 'f.name', array('tree' => true)), 'fatherName')
            ->addField(new Field('father.father.name', 'string', 'Grand Father', 'gf.name', array('tree' => true)), 'grandFatherName')
        ;

        $wrapper->setOrderBy(array('name' => 'asc', 'fatherName' => 'asc', 'grandFatherName' => 'asc'));

        // Datagrid
        $datagrid = $this->get('gloomy.datagrid')->factory($wrapper);

        return array('datagrid' => $datagrid);
    }
}
```

PHP Entity
``` php
<?php

namespace Gloomy\ExemplesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(  targetEntity="Person"
     *                  )
     * @ORM\JoinColumn( name="father",
     *                  referencedColumnName="id",
     *                  nullable=true
     *                  )
     */
    protected $father;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFather()
    {
        return $this->father;
    }
}
```


Twig template
``` html+django
{% extends "::base.html.twig" %}

{% block body %}
    {{ datagrid(datagrid) }}
{% endblock %}
```