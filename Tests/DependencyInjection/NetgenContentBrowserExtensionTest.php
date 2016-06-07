<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection;

use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class NetgenContentBrowserExtensionTest extends AbstractExtensionTestCase
{
    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(
            new NetgenContentBrowserExtension(),
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     */
    public function testParameters()
    {
        $this->container->setParameter('kernel.bundles', array());

        $this->load(
            array(
                'items' => array(
                    'ezcontent' => array(
                        'converter' => 'converter',
                        'backend' => 'backend',
                        'root_items' => array(),
                    )
                )
            )
        );

        $this->assertContainerBuilderHasParameter('netgen_content_browser.route_prefix', '/cb');
        $this->assertContainerBuilderHasParameter('netgen_content_browser.config.ezcontent');
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     */
    public function testServices()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'EzPublishCoreBundle' => 'NetgenTagsBundle',
                'NetgenTagsBundle' => 'NetgenTagsBundle'
            )
        );

        $this->load();

        $this->assertContainerBuilderHasService('netgen_content_browser.item_builder');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.ezlocation');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.eztags');

        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_converter');
        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_backend');
        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_config');

        $this->assertContainerBuilderHasService('netgen_content_browser.config_loader.default');
        $this->assertContainerBuilderHasService('netgen_content_browser.config_loader.chained');
        $this->assertContainerBuilderHasAlias(
            'netgen_content_browser.config_loader',
            'netgen_content_browser.config_loader.chained'
        );
    }

    /**
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     */
    public function testServicesWithoutBundles()
    {
        $this->container->setParameter('kernel.bundles', array());

        $this->load();

        $this->assertContainerBuilderHasService('netgen_content_browser.item_builder');

        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_converter');
        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_backend');
        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_config');

        $this->assertContainerBuilderHasService('netgen_content_browser.config_loader.default');
        $this->assertContainerBuilderHasService('netgen_content_browser.config_loader.chained');
        $this->assertContainerBuilderHasAlias(
            'netgen_content_browser.config_loader',
            'netgen_content_browser.config_loader.chained'
        );
    }
}
