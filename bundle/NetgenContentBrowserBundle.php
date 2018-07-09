<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle;

use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenContentBrowserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompilerPass\ItemTypePass());
        $container->addCompilerPass(new CompilerPass\ColumnProviderPass());
        $container->addCompilerPass(new CompilerPass\EzPublishDefaultPreviewPass());
    }
}
