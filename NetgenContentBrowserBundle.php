<?php

namespace Netgen\Bundle\ContentBrowserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendRegistryPass;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ValueLoaderRegistryPass;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemBuilderPass;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemRendererPass;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ChainedConfigLoaderPass;

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
        $container->addCompilerPass(new ChainedConfigLoaderPass());
        $container->addCompilerPass(new BackendRegistryPass());
        $container->addCompilerPass(new ValueLoaderRegistryPass());
        $container->addCompilerPass(new ItemBuilderPass());
        $container->addCompilerPass(new ItemRendererPass());
        $container->addCompilerPass(new ColumnProviderPass());
    }
}
