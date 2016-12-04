<?php

namespace Netgen\Bundle\ContentBrowserBundle;

use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
        $container->addCompilerPass(new CompilerPass\ConfigLoaderPass());
        $container->addCompilerPass(new CompilerPass\BackendRegistryPass());
        $container->addCompilerPass(new CompilerPass\ItemSerializerPass());
        $container->addCompilerPass(new CompilerPass\ItemRendererPass());
        $container->addCompilerPass(new CompilerPass\ColumnProviderPass());
        $container->addCompilerPass(new CompilerPass\EzPublishDefaultPreviewPass());
    }
}
