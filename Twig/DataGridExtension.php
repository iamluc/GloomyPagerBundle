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

            'datagrid_row_order_by'    => new \Twig_Function_Method($this, 'renderRowOrderBy', array('is_safe' => array('html'))),
            'datagrid_row_filters'     => new \Twig_Function_Method($this, 'renderRowFilters', array('is_safe' => array('html'))),
            'datagrid_row_values'      => new \Twig_Function_Method($this, 'renderRowValues', array('is_safe' => array('html'))),

            'datagrid_column_order_by' => new \Twig_Function_Method($this, 'renderColumnOrderBy', array('is_safe' => array('html'))),
            'datagrid_column_filter'   => new \Twig_Function_Method($this, 'renderColumnFilter', array('is_safe' => array('html'))),
            'datagrid_column_action'   => new \Twig_Function_Method($this, 'renderColumnAction', array('is_safe' => array('html'))),
            'datagrid_column_value'    => new \Twig_Function_Method($this, 'renderColumnValue', array('is_safe' => array('html'))),

            'datagrid_item_value'      => new \Twig_Function_Method($this, 'renderItemValue'),
        );
    }

    public function setTheme($datagrid, array $resources)
    {
        $this->_themes->attach($datagrid, $resources);
    }

    protected function render($datagrid, $blocks, $params = array())
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $templates = array(self::$defaultTheme);
        if (isset($this->_themes[$datagrid])) {
            $templates = array_merge($this->_themes[$datagrid], $templates);
        }

        foreach ($templates as $template) {
            if (!$template instanceof \Twig_Template) {
                $template = $this->_environment->loadTemplate($template);
            }
            foreach ($blocks as $block) {
                if ($template->hasBlock($block)) {
                    return $template->renderBlock($block, array_merge($params, array('datagrid' => $datagrid)));
                }
            }
        }

        throw new \Exception('Block '.$block.' not found');
    }

    public function renderDataGrid($datagrid, $title = null, $params = array())
    {
        if (!is_null($title)) {
            $datagrid->setTitle($title);
        }
        return $this->render($datagrid, 'datagrid', $params);
    }

    public function renderJavascripts($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_javascripts', $params);
    }

    public function renderStyleSheets($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_stylesheets', $params);
    }

    public function renderContent($datagrid, $title = null, $params = array())
    {
        if (!is_null($title)) {
            $datagrid->setTitle($title);
        }
        return $this->render($datagrid, 'datagrid_content', $params);
    }

    public function renderHeader($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_header', $params);
    }

    public function renderBody($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_body', $params);
    }

    public function renderFooter($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_footer', $params);
    }

    public function renderPaginate($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_paginate', $params);
    }

    public function renderItemsPerPage($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_items_per_page', $params);
    }

    public function renderRowOrderBy($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_row_order_by', $params);
    }

    public function renderRowFilters($datagrid, $params = array())
    {
        return $this->render($datagrid, 'datagrid_row_filters', $params);
    }

    public function renderRowValues($datagrid, $item, $params = array())
    {
        return $this->render($datagrid, 'datagrid_row_values', array_merge($params, array('item' => $item)));
    }

    public function renderColumnOrderBy($datagrid, $field, $alias, $params = array())
    {
        $blocks = array('datagrid_column_order_by__'.$this->sanitizeAlias($alias), 'datagrid_column_order_by');
        return $this->render($datagrid, $blocks, array_merge($params, array('field' => $field, 'alias' => $alias)));
    }

    public function renderColumnFilter($datagrid, $field, $alias, $params = array())
    {
        $blocks = array('datagrid_column_filter__'.$this->sanitizeAlias($alias), 'datagrid_column_filter');
        return $this->render($datagrid, $blocks, array_merge($params, array('field' => $field, 'alias' => $alias)));
    }

    public function renderColumnAction($datagrid, $item, $params = array())
    {
        return $this->render($datagrid, 'datagrid_column_action', array_merge($params, array('item' => $item)));
    }

    public function renderColumnValue($datagrid, $field, $item, $alias, $params = array())
    {
        $blocks = array('datagrid_column_value__'.$this->sanitizeAlias($alias), 'datagrid_column_value');
        return $this->render($datagrid, $blocks, array_merge($params, array('item' => $item, 'field' => $field, 'alias' => $alias)));
    }

    public function renderItemValue($datagrid, $field, $item)
    {
        return $field->readData($item);
    }

    protected function sanitizeAlias($alias)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $alias);
    }
}
