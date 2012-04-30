GloomyPagerBundle
=================

NEWS
----

- Added Crud on top of the DataGrid (Automatic Create/Retrieve/Update/Delete on Entities)
- Added DataGrid on top of the Pager (Automatic pager templating)

DEMO
----
**Test it on [our demo website](http://iamluc.legtux.org/web/demo1)**
(You can download the demonstration website [on github](https://github.com/iamluc/DemoSite))

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

LGPL v2

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

##1. Download the bundle

**Using deps file**

Add to deps file

```
[GloomyPagerBundle]
    git=git://github.com/iamluc/GloomyPagerBundle
    target=bundles/Gloomy/PagerBundle
```

**OR downloading from github**

Download the bundle ([https://github.com/iamluc/GloomyPagerBundle](https://github.com/iamluc/GloomyPagerBundle)) in vendor/bundles/Gloomy (create if not exists) and rename the folder to PagerBundle

    You must have a tree similar to vendor/bundles/Gloomy/PagerBundle

##2. Add to app/autoload.php :

    'Gloomy'           => __DIR__.'/../vendor/bundles',

##3. Add to app/AppKernel.php

    new Gloomy\PagerBundle\GloomyPagerBundle(),

##4. Install assets (Optional)

    php app/console assets:install --symlink web
