<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class NetgenContentBrowserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $extensionAlias = $this->getAlias();

        $configuration = new Configuration($extensionAlias);
        $config = $this->processConfiguration($configuration, $configs);

        $availableItemTypes = array();

        foreach ($config['item_types'] as $itemType => $itemConfig) {
            $definition = new DefinitionDecorator('netgen_content_browser.config');
            $definition
                ->replaceArgument(0, $itemType)
                ->replaceArgument(1, $itemConfig);

            $container->setDefinition(
                'netgen_content_browser.config.' . $itemType,
                $definition
            );

            $availableItemTypes[$itemType] = $itemConfig['name'];
        }

        $container->setParameter('netgen_content_browser.item_types', $availableItemTypes);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('SyliusCoreBundle', $activatedBundles)) {
            $loader->load('sylius/product/services.yml');
        }

        if (in_array('EzPublishCoreBundle', $activatedBundles)) {
            $loader->load('ezplatform/services.yml');
        }

        if (in_array('NetgenTagsBundle', $activatedBundles)) {
            $loader->load('eztags/services.yml');
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');

        $this->doPrepend($container, 'framework/twig.yml', 'twig');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('SyliusCoreBundle', $activatedBundles)) {
            $this->doPrepend($container, 'sylius/product/config.yml', 'netgen_content_browser');
        }

        if (in_array('EzPublishCoreBundle', $activatedBundles)) {
            $this->doPrepend($container, 'ezplatform/config.yml', 'netgen_content_browser');
            $this->doPrepend($container, 'ezplatform/image.yml', 'ezpublish');

            if (in_array('NetgenTagsBundle', $activatedBundles)) {
                $this->doPrepend($container, 'eztags/config.yml', 'netgen_content_browser');
            }
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $fileName
     * @param string $configName
     */
    protected function doPrepend(ContainerBuilder $container, $fileName, $configName)
    {
        $configFile = __DIR__ . '/../Resources/config/' . $fileName;
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig($configName, $config);
        $container->addResource(new FileResource($configFile));
    }
}
