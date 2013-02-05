A small exemple to explain how to make AJAX calls

PHP Controller
``` php
<?php

namespace Gloomy\ExemplesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/exemple1")
     */
    public function indexAction()
    {
        $colors = array(
            array('id' => 1, 'color' => 'blue'),
            array('id' => 2, 'color' => 'red'),
            array('id' => 3, 'color' => 'orange'),
            array('id' => 4, 'color' => 'black'),
            array('id' => 5, 'color' => 'green'),
            array('id' => 6, 'color' => 'yellow'),
            array('id' => 7, 'color' => 'grey'),
            array('id' => 8, 'color' => 'white'),
            array('id' => 9, 'color' => 'pink'),
            array('id' => 10, 'color' => 'cyan'),
            array('id' => 11, 'color' => 'maroon'),
            );

        $datagrid = $this->get('gloomy.datagrid')->factory($colors);
        $template = $this->getRequest()->isXMLHttpRequest() ? 'datagrid.html.twig' : 'shell.html.twig';

        return $this->render(
            'GloomyExemplesBundle:Default:'.$template,
            array('datagrid' => $datagrid)
        );
    }
}
```

First twig template (shell.html.twig)
``` html+django

{% extends "::base.html.twig" %}

{% block stylesheets %}
    {{ parent() }}
    {{ datagrid_stylesheets(datagrid) }}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {{ datagrid_javascripts(datagrid) }}

    <script src="/bundles/gloomypager/js/gloomy-utils.js" type="text/javascript"></script>

    <script language="javascript">
        updateDatagrid = function(url, data) {
            gloomyAjaxUpdater(url, '#datagrid', {spinner: '#loading', type: 'post', data: data, onsuccess: function() { bindDatagrid() }});
        }

        bindDatagrid = function() {
            $('#datagrid form').on(
                'submit',
                function(event) {
                    event.preventDefault();
                    updateDatagrid($(this).attr('action'), $(this).serializeArray());
                }
            );

            $('#datagrid a:not(.outbound, [href^="#"])').on(
                'click',
                function(event) {
                    event.preventDefault();
                    updateDatagrid(this.href);
                }
            );
        }

        jQuery(document).ready(function() {
            bindDatagrid();
        })
    </script>
{% endblock %}

{% block body %}
    <h1>
        Exemple 1
        <span id="loading" style="color: orange; float: right; display: none; font-size: 0.5em;">
            Loading...
        </span>
    </h1>

    <div id="datagrid">
        {% include 'GloomyExemplesBundle:Default:datagrid.html.twig' with {'datagrid': datagrid} %}
    </div>
{% endblock %}
```

Second twig template (datagrid.html.twig)
``` html+django
{{ datagrid_content(datagrid) }}
```
