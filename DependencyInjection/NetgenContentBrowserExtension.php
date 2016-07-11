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

        foreach ($config['configs'] as $configName => $configValues) {
            $definition = new DefinitionDecorator('netgen_content_browser.config');
            $definition
                ->replaceArgument(0, $configName)
                ->replaceArgument(1, $configValues);

            $container->setDefinition(
                'netgen_content_browser.config.' . $configName,
                $definition
            );
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

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

        $loader->load('services.yml');
        $loader->load('default_settings.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        $this->doPrepend($container, 'framework/twig.yml', 'twig');

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
