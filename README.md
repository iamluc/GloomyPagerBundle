GloomyPagerBundle
=================

ABOUT
-----

> Note:

> This bundle has been replaced by the [DatathekePagerBundle](https://github.com/datatheke/DatathekePagerBundle)

> You should use it instead

The GloomyPagerBundle allows you to display data with pagination, and to easily order and filter them.

**4 services are available :**
- Pager lets you manipulate resources like an Array or an Entity, but you keep total control of your template.
- DataGrid allows you to render a default template. But you can of course customize each part of it. It's built on top of the Pager.
- Crud adds create/edit/delete views in addition of the DataGrid view. It's built on top of the DataGrid.
- REST Connectors let you connect to a JavaScript grid or autocompleter easily (like jqGrid, DataTables, Autocomplete, Typeahead etc...)

**Features are :**
- Many wrappers
```
    Array
    Entity / ORM QueryBuilder
    DBAL QueryBuilder
    MongoDB
    Datatheke.com
    Null
```

- Advanced filtering (AND/OR);
``` php
<?php
    //...
    $filters   = array( 'operator'    => 'and',
                        'criteria'    => array( array(  'field' => 'job', 'operator' => 'contains', 'value' => 'Director' ),
                                                array(  'operator'    => 'or',
                                                        'criteria'    => array(
                                                                array( 'field' => 'firstname', 'operator' => 'contains', 'value' => 'y' ),
                                                                array( 'field' => 'moviesNb', 'operator' => 'less', 'value' => 30 ),

                                                        )
                                                    )
                                                )
                        );
```

- Multiple ordering (order by lastname asc, firstname asc)
- Ajax compatible
- Many pagers on the same page

LICENSE
-------

MIT

EXEMPLES
--------
More exemples in the [documentation](https://github.com/iamluc/GloomyPagerBundle/tree/master/Resources/doc)

##Crud

PHP

``` php
<?php
    //...
    /**
     * @Template()
     */
    public function crudAction()
    {
        return $this->get('gloomy.crud')->factory('MyBundle:MyEntity')->handle();
    }
```

TWIG

``` html+django
    {{ crud(crud) }}
```

##Datagrid

PHP

``` php
<?php
    //...
    /**
     * @Template()
     */
    public function dataGridAction()
    {
        return array('datagrid' => $this->get('gloomy.datagrid')->factory('MyBundle:MyEntity'));
    }
```

TWIG

``` html+django
    {{ datagrid(datagrid) }}
```

##Pager

PHP

``` php
<?php
    //...
    /**
     * @Template()
     */
    public function pagerAction()
    {
        return array('pager' => $this->get('gloomy.pager')->factory('MyBundle:MyEntity'));
    }
```

TWIG

``` html+django
    {% extends "::base.html.twig" %}

    {% import 'GloomyPagerBundle:Pager:macros.html.twig' as helper %}

    {% block stylesheets %}
        {{ parent() }}
        {{ helper.stylesheets() }}
    {% endblock %}

    {% block javascripts %}
        {{ parent() }}
        {{ helper.javascripts() }}
    {% endblock %}

    {% block body %}
        <form action="{{ pager.pathForm() }}" method="post">
            <table class="table table-striped table-bordered table-hover">
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

            {# Allows submitting filters with 'Enter' #}
            <input type="image" src="{{ asset('bundles/gloomypager/img/transparent.gif') }}" height="0" width="0" border="0">

            {{ helper.paginate( pager ) }}
        </form>
    {% endblock %}
```

INSTALLATION
------------

### 1. Install with composer

    composer.phar require "gloomy/pager-bundle" "dev-master"

### 2. Modify your app/AppKernel.php

``` php
<?php
    //...
    $bundles = array(
        //...
        new Gloomy\PagerBundle\GloomyPagerBundle(),
    );
```

### 3. Install assets (Optional)

    php app/console assets:install web --symlink
