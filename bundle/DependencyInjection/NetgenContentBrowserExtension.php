<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Netgen\ContentBrowser\Config\Configuration as BrowserConfiguration;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
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

        $availableItemTypes = [];

        foreach ($config['item_types'] as $itemType => $itemConfig) {
            if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $itemType) !== 1) {
                throw new RuntimeException(
                    'Item type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                );
            }

            $configParameters = $itemConfig['parameters'];
            unset($itemConfig['parameters']);

            $container->register('netgen_content_browser.config.' . $itemType, BrowserConfiguration::class)
                ->setPublic(true)
                ->addArgument($itemType)
                ->addArgument($itemConfig)
                ->addArgument($configParameters);

            $availableItemTypes[$itemType] = $itemConfig['name'];
        }

        $container->setParameter('netgen_content_browser.item_types', $availableItemTypes);

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
        return new Configuration();
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
