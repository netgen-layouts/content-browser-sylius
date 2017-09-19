<?php

namespace Netgen\Bundle\ContentBrowserBundle;

use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenContentBrowserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass\BackendRegistryPass());
        $container->addCompilerPass(new CompilerPass\ColumnProviderPass());
        $container->addCompilerPass(new CompilerPass\EzPublishDefaultPreviewPass());
    }
}
