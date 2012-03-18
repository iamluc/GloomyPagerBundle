GloomyPagerBundle
=================

ABOUT
-----

The GloomyPagerBundle allows you to display datas with pagination, and to easily order and filter them.

Test it on http://iamluc.legtux.org/web/demo1

(You can download the demonstration website at https://github.com/iamluc/DemoSite)

It sits on top of PagerWrapper (Array and ORM QueryBuilder for now).

Characteristics are :

* Advanced filtering (AND/OR)

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

* Multiples ordering (order by lastname asc, firstname asc)
* Ajax compatible
* Many pager on the same page

EXEMPLES
--------

PHP CODE

    static protected $persons = array(
            array(  'firstname' => 'Tim',
                    'lastname'  => 'Burton',
                    'job'       => 'Director',
                    'moviesNb'  => 28
                    ),
    […]

    public function demo1Action()
    {
        return $this->render('GloomyDemoSiteBundle::demo1.html.twig', array('pager' => $$this->get('gloomy.pager')->factory(self::$persons, 'demo1')));
    }

TWIG

    {% extends 'GloomyDemoSiteBundle:Common:layout.html.twig' %}

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

NOTE :
the bundle requires Zend_Paginator (from Zend Framework 2 Minimal)
the QueryBuilderWraper works better with Doctrine 2.2


1) Download the bundle (https://github.com/iamluc/Gloomy) in vendor/bundles/
You must have a tree similar to vendor/bundles/Gloomy/PagerBundle

2) Download Zend Framework and uncompress it in vendor/
You must have a tree similar to vendor/Zend/library/Zend

3) Add to app/autoload.php :

    'Zend'             => __DIR__.'/../vendor/Zend/library',
    'Gloomy'           => __DIR__.'/../vendor/bundles',

4) Add to app/AppKernel.php

    new Gloomy\PagerBundle\GloomyPagerBundle(),

5) Install assets

    php app/console assets:install web