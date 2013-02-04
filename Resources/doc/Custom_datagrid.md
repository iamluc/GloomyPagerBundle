HOW TO : customize a datagrid
=============================

With this exemple, you will know how to customize fields (show/hide, change label) and how to customize rendering (values & filters)

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
     * @Route("/exemple2")
     * @Template()
     */
    public function exemple2Action()
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

        // Customize Fields
        $datagrid->getField('id')->hide(); // Or $datagrid->showOnly(array('color'));
        $datagrid->getField('color')->setLabel('Favourites colors')->addOption('select_options', $colors);

        return array('datagrid' => $datagrid);
    }
}

```

Twig template (exemple2.html.twig)
``` html+django
{% extends "::base.html.twig" %}

{% datagrid_theme datagrid _self %}

{% block body %}
    <h1>Exemple 2</h1>
    {{ datagrid(datagrid) }}
{% endblock %}

{% block datagrid_column_value__color %}
    <td>
        <span style="color: {{ datagrid_item_value(datagrid, field, item) }};">{{ datagrid_item_value(datagrid, field, item) }}</span>
    </td>
{% endblock %}

{% block datagrid_column_filter__color %}
    <th>
        <input type="hidden" name="{{ datagrid.pager.getConfig('filtersVar') }}[f][color]" value="color" />
        <input type="hidden" name="{{ datagrid.pager.getConfig('filtersVar') }}[o][color]" value="equals" />

        <select name="{{ datagrid.pager.getConfig('filtersVar') }}[v][color]" onchange="this.form.submit();">
            <option value="">-- Choose a color --</option>
            {% for row in field.getOption('select_options') %}
                <option value="{{ row.color }}" {% if datagrid.pager.getValue('filtersVar').v['color']|default('') == row.color %}selected="selected"{% endif %}>{{ row.color }}</option>
            {% endfor %}
        </select>
    </th>
{% endblock %}
```