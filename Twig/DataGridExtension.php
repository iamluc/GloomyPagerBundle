<?php

namespace Gloomy\PagerBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Gloomy\PagerBundle\Twig\TokenParser\DatagridThemeTokenParser;

class DataGridExtension extends \Twig_Extension
{
    protected static $defaultTheme = 'GloomyPagerBundle:DataGrid:blocks.html.twig';

    protected $_generator;

    protected $_environment;

    protected $_themes;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->_generator = $generator;
        $this->_themes = new \SplObjectStorage();
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->_environment = $environment;
    }

    public function getName()
    {
        return 'DataGridExtension';
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
                new DatagridThemeTokenParser(),
        );
    }

    public function getFunctions()
    {
        return array(
            'datagrid'                 => new \Twig_Function_Method($this, 'renderDataGrid', array('is_safe' => array('html'))),
            'datagrid_javascripts'     => new \Twig_Function_Method($this, 'renderJavascripts', array('is_safe' => array('html'))),
            'datagrid_stylesheets'     => new \Twig_Function_Method($this, 'renderStyleSheets', array('is_safe' => array('html'))),
            'datagrid_content'         => new \Twig_Function_Method($this, 'renderContent', array('is_safe' => array('html'))),

            'datagrid_header'          => new \Twig_Function_Method($this, 'renderHeader', array('is_safe' => array('html'))),
            'datagrid_body'            => new \Twig_Function_Method($this, 'renderBody', array('is_safe' => array('html'))),
            'datagrid_footer'          => new \Twig_Function_Method($this, 'renderFooter', array('is_safe' => array('html'))),
            'datagrid_paginate'        => new \Twig_Function_Method($this, 'renderPaginate', array('is_safe' => array('html'))),
            'datagrid_items_per_page'  => new \Twig_Function_Method($this, 'renderItemsPerPage', array('is_safe' => array('html'))),

            'datagrid_column_order_by' => new \Twig_Function_Method($this, 'renderColumnOrderBy', array('is_safe' => array('html'))),
            'datagrid_column_filter'   => new \Twig_Function_Method($this, 'renderColumnFilter', array('is_safe' => array('html'))),
            'datagrid_column_value'    => new \Twig_Function_Method($this, 'renderColumnValue', array('is_safe' => array('html'))),

            'datagrid_item_value'      => new \Twig_Function_Method($this, 'renderItemValue'),
        );
    }

    public function setTheme($datagrid, array $resources)
    {
        $this->_themes->attach($datagrid, $resources);
    }

    protected function render($datagrid, $block, $params = array())
    {
        $templates = array(self::$defaultTheme);
        if (isset($this->_themes[$datagrid])) {
            $templates = array_merge($this->_themes[$datagrid], $templates);
        }

        foreach ($templates as $template) {
            if (!$template instanceof \Twig_Template) {
                $template = $this->_environment->loadTemplate($template);
            }
            if ($template->hasBlock($block)) {
                return $template->renderBlock($block, array_merge($params, array('datagrid' => $datagrid)));
            }
        }

        throw new \Exception('Block '.$block.' not found');
    }

    public function renderDataGrid($datagrid)
    {
        return $this->render($datagrid, 'datagrid');
    }

    public function renderJavascripts($datagrid)
    {
        return $this->render($datagrid, 'datagrid_javascripts');
    }

    public function renderStyleSheets($datagrid)
    {
        return $this->render($datagrid, 'datagrid_stylesheets');
    }

    public function renderContent($datagrid)
    {
        return $this->render($datagrid, 'datagrid_content');
    }

    public function renderHeader($datagrid)
    {
        return $this->render($datagrid, 'datagrid_header');
    }

    public function renderBody($datagrid)
    {
        return $this->render($datagrid, 'datagrid_body');
    }

    public function renderFooter($datagrid)
    {
        return $this->render($datagrid, 'datagrid_footer');
    }

    public function renderPaginate($datagrid)
    {
        return $this->render($datagrid, 'datagrid_paginate');
    }

    public function renderItemsPerPage($datagrid)
    {
        return $this->render($datagrid, 'datagrid_items_per_page');
    }

    public function renderColumnOrderBy($datagrid, $field)
    {
        return $this->render($datagrid, 'datagrid_column_order_by', array('field' => $field));
    }

    public function renderColumnFilter($datagrid, $field)
    {
        return $this->render($datagrid, 'datagrid_column_filter', array('field' => $field));
    }

    public function renderColumnValue($datagrid, $field, $item)
    {
        return $this->render($datagrid, 'datagrid_column_value', array('item' => $item, 'field' => $field));
    }

    public function renderItemValue($datagrid, $field, $item)
    {
        if (is_array($item)) {
            $value = $item[$field->getName()];
        }
        else {
            $value = call_user_func_array(array($item, 'get'.ucfirst($field->getName())), array());
        }

        if ($value instanceOf \DateTime) {
            return $value->format($field->getDateFormat());
        }
//         elseif ($field->getType() === 'date') {

//         }

        return $value;
    }
}