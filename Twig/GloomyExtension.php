<?php

namespace Gloomy\PagerBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GloomyExtension extends \Twig_Extension
{
    private $_generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->_generator = $generator;
    }

    public function getName()
    {
        return 'GloomyExtension';
    }

    public function getFunctions()
    {
        return array(
            'array_combine'     => new \Twig_Function_Method($this, 'array_combine'),
            'array_merge'       => new \Twig_Function_Method($this, 'array_merge'),
            'uniqid'            => new \Twig_Function_Method($this, 'uniqid'),
            'print_r'           => new \Twig_Function_Method($this, 'print_r'),
            'get_class'         => new \Twig_Function_Method($this, 'get_class'),
            'instanceof'        => new \Twig_Function_Method($this, 'instance_of'),
            'last'              => new \Twig_Function_Method($this, 'last'),
            'highlight_string'  => new \Twig_Function_Method($this, 'highlight_string'),
        );
    }

    public function array_combine(array $keys, array $values)
    {
        return array_combine($keys, $values);
    }

    public function array_merge(array $arr1, array $arr2)
    {
        return array_merge($arr1, $arr2);
    }

    public function uniqid($prefix = '', $more_entropy = false)
    {
        return uniqid($prefix, $more_entropy);
    }

    public function print_r($expression)
    {
        return print_r($expression, true);
    }

    public function get_class($object)
    {
        if (!is_object($object)) {
            return '';
        }
        return get_class($object);
    }

    public function instance_of($object, $class_name)
    {
        if (!is_object($object)) {
            return false;
        }
        return ($object instanceof $class_name);
    }

    public function last($array)
    {
        return end($array);
    }

    public function highlight_string($expression)
    {
        return highlight_string($expression, true);
    }
}
