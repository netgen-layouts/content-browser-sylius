<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class NetgenContentBrowserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            sprintf('%s.%s', $this->getAlias(), 'item_types'),
            $config['item_types']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('SyliusCoreBundle', $activatedBundles, true)) {
            $loader->load('sylius/product/services.yml');
            $loader->load('sylius/taxon/services.yml');
        }

        if (in_array('EzPublishCoreBundle', $activatedBundles, true)) {
            $loader->load('ezplatform/services.yml');
        }

        if (in_array('NetgenTagsBundle', $activatedBundles, true)) {
            $loader->load('eztags/services.yml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');

        $this->doPrepend($container, 'framework/twig.yml', 'twig');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('SyliusCoreBundle', $activatedBundles, true)) {
            $this->doPrepend($container, 'sylius/product/config.yml', 'netgen_content_browser');
            $this->doPrepend($container, 'sylius/taxon/config.yml', 'netgen_content_browser');
        }

        if (in_array('EzPublishCoreBundle', $activatedBundles, true)) {
            $this->doPrepend($container, 'ezplatform/config.yml', 'netgen_content_browser');
            $this->doPrepend($container, 'ezplatform/image.yml', 'ezpublish');

            if (in_array('NetgenTagsBundle', $activatedBundles, true)) {
                $this->doPrepend($container, 'eztags/config.yml', 'netgen_content_browser');
            }
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    private function doPrepend(ContainerBuilder $container, string $fileName, string $configName): void
    {
        $configFile = __DIR__ . '/../Resources/config/' . $fileName;
        $config = Yaml::parse((string) file_get_contents($configFile));
        $container->prependExtensionConfig($configName, $config);
        $container->addResource(new FileResource($configFile));
    }
}
