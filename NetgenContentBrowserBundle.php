<?php

namespace Netgen\Bundle\ContentBrowserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemBuilderCompilerPass;

class NetgenContentBrowserBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ItemBuilderCompilerPass());
    }
}
