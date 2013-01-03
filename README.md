GloomyPagerBundle
=================

ABOUT
-----

The GloomyPagerBundle allows you to display datas with pagination, and to easily order and filter them.

**3 services are availables :**
- Pager lets you manipulate ressources like Array or Entity, but you keep total control of your template.
- DataGrid allows you to render a default template. But you can of course customize each parts of it. It sits on top of the Pager.
- Crud adds create/edit/delete views in addition of the DataGrid view. It sits on top of the DataGrid.

**Features of the Pager Wrappers (Array, Entity/ORM QueryBuilder) are :**
- Advanced filtering (AND/OR);

>     $filters   = array( 'operator'    => 'and',
>                        'criteria'    => array( array(  'field' => 'job', 'operator' => 'contains', 'value' => 'Director' ),
>                                                array(  'operator'    => 'or',
>                                                        'criteria'    => array(
>                                                                array( 'field' => 'firstname', 'operator' => 'contains', 'value' => 'y' ),
>                                                                array( 'field' => 'moviesNb', 'operator' => 'less', 'value' => 30 ),
>
>                                                        )
>                                                    )
>                                                )
>                        );

- Multiples ordering (order by lastname asc, firstname asc)
- Ajax compatible
- Many pager on the same page

LICENSE
-------

MIT

EXEMPLES
--------

##Crud

PHP

    /**
     * @Template()
     */
    public function crudAction()
    {
        return $this->get('gloomy.crud')->factory('MyBundle:MyEntity')->handle();
    }
    
TWIG

    {{ crud(crud) }}

##Datagrid

PHP 

    /**
     * @Template()
     */
    public function dataGridAction()
    {
        return array('datagrid' => $this->get('gloomy.datagrid')->factory('MyBundle:MyEntity'));
    }

TWIG

    {{ datagrid(datagrid) }}

##Pager

PHP

    /**
     * @Template()
     */
    public function pagerAction()
    {
        return array('pager' => $this->get('gloomy.pager')->factory('MyBundle:MyEntity'));
    }

TWIG

    {% import 'GloomyPagerBundle:Pager:macros.html.twig' as helper %}

    {% block contenu %}

        {% block description %}{% endblock %}

        <form action="{{ pager.pathForm() }}"
            method="post"
            id="formPager"
            >

            <div class="content" >

                <table class="gloomy-pager">
                <thead>
                    <tr>
                        <th>{{ helper.orderBy( pager, 'firstname', 'Firstname' ) }}</th>
                        <th>{{ helper.orderBy( pager, 'lastname', 'Lastname' ) }}</th>
                        <th>{{ helper.orderBy( pager, 'job', 'Job' ) }}</th>
                        <th>{{ helper.orderBy( pager, 'moviesNb', 'Number of movies' ) }}</th>
                    </tr>
                    <tr>
                        <th>{{ helper.filter( pager, 'firstname' ) }}</th>
                        <th>{{ helper.filter( pager, 'lastname' ) }}</th>
                        <th>{{ helper.filter( pager, 'job' ) }}</th>
                        <th>{{ helper.filter( pager, 'moviesNb' ) }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        {% for person in pager.items %}
                        <tr>
                            <td>{{ person.firstname }}</td>
                            <td>{{ person.lastname }}</td>
                            <td>{{ person.job }}</td>
                            <td>{{ person.moviesNb }}</td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>

                {{ helper.paginate( pager ) }}

            </div>

        </form>

    {% endblock %}

INSTALLATION
------------

### 1. Modify your composer.json

``` yaml
{
    "require": {
        "gloomy/pager-bundle": "dev-master"
    }
}
```

### 2. Run update

    php composer.phar update gloomy/pager-bundle

### 3. Modify your app/AppKernel.php

``` php
<?php
    //...
    $bundles = array(
        //...
        new Gloomy\PagerBundle\GloomyPagerBundle(),
    );
```

### 4. Install assets (Optional)

    php app/console assets:install web --symlink
