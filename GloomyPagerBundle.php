<?php

namespace Gloomy\PagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Gloomy\PagerBundle\DependencyInjection\ContainerBuilder\SerializerCompilerPass;

class GloomyPagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SerializerCompilerPass());
    }
}
