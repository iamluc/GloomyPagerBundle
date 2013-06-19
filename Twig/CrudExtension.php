<?php

namespace Gloomy\PagerBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Gloomy\PagerBundle\Twig\TokenParser\CrudThemeTokenParser;

class CrudExtension extends \Twig_Extension
{
    protected static $defaultTheme = 'GloomyPagerBundle:Crud:blocks.html.twig';

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
        return 'CrudExtension';
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
                new CrudThemeTokenParser(),
        );
    }

    public function getFunctions()
    {
        return array(
            'crud'             => new \Twig_Function_Method($this, 'renderCrud', array('is_safe' => array('html'))),
            'crud_javascripts' => new \Twig_Function_Method($this, 'renderJavascripts', array('is_safe' => array('html'))),
            'crud_stylesheets' => new \Twig_Function_Method($this, 'renderStyleSheets', array('is_safe' => array('html'))),
            'crud_content'     => new \Twig_Function_Method($this, 'renderContent', array('is_safe' => array('html'))),

            'crud_list'        => new \Twig_Function_Method($this, 'renderCrudList', array('is_safe' => array('html'))),
            'crud_add'         => new \Twig_Function_Method($this, 'renderCrudAdd', array('is_safe' => array('html'))),
            'crud_edit'        => new \Twig_Function_Method($this, 'renderCrudEdit', array('is_safe' => array('html'))),
            'crud_form'        => new \Twig_Function_Method($this, 'renderCrudForm', array('is_safe' => array('html'))),
        );
    }

    public function setTheme($crud, array $resources)
    {
        $this->_themes->attach($crud, $resources);
    }

    protected function render($crud, $block, array $params = array())
    {
        $templates = array(self::$defaultTheme);
        if (isset($this->_themes[$crud])) {
            $templates = array_merge($this->_themes[$crud], $templates);
        }

        foreach ($templates as $template) {
            if (!$template instanceof \Twig_Template) {
                $template = $this->_environment->loadTemplate($template);
            }
            if ($template->hasBlock($block)) {
                return $template->renderBlock($block, array_merge($params, array('crud' => $crud)));
            }
        }

        throw new \Exception('Block '.$block.' not found');
    }

    public function renderCrud($crud, $title = null)
    {
        if (!is_null($title)) {
            $crud->setTitle($title);
        }
        return $this->render($crud, 'crud');
    }

    public function renderJavascripts($crud)
    {
        return $this->render($crud, 'crud_javascripts');
    }

    public function renderStyleSheets($crud)
    {
        return $this->render($crud, 'crud_stylesheets');
    }

    public function renderContent($crud, $title = null)
    {
        if (!is_null($title)) {
            $crud->setTitle($title);
        }
        return $this->render($crud, 'crud_content');
    }

    public function renderCrudList($crud)
    {
        return $this->render($crud, 'crud_list');
    }

    public function renderCrudAdd($crud)
    {
        return $this->render($crud, 'crud_add', $crud->getFormDatas());
    }

    public function renderCrudEdit($crud)
    {
        return $this->render($crud, 'crud_edit', $crud->getFormDatas());
    }

    public function renderCrudForm($crud, $form)
    {
        return $this->render($crud, 'crud_form', array('form' => $form));
    }
}
