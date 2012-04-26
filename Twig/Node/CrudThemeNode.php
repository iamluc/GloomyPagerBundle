<?php

namespace Gloomy\PagerBundle\Twig\Node;

class CrudThemeNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $crud, \Twig_NodeInterface $resources, $lineno, $tag = null)
    {
        parent::__construct(array('crud' => $crud, 'resources' => $resources), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->env->getExtension(\'CrudExtension\')->setTheme(')
            ->subcompile($this->getNode('crud'))
            ->raw(', array(')
        ;

        foreach ($this->getNode('resources') as $resource) {
            $compiler
                ->subcompile($resource)
                ->raw(', ')
            ;
        }

        $compiler->raw("));\n");
    }
}
