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
     * We test for existence of one service from each of the config files.
     *
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     */
    public function testServices()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'EzPublishCoreBundle' => 'EzPublishCoreBundle',
                'NetgenTagsBundle' => 'NetgenTagsBundle',
                'SyliusCoreBundle' => 'SyliusCoreBundle',
            )
        );

        $this->load(
            array(
                'item_types' => array(
                    'ezcontent' => array(
                        'name' => 'item_types.ezcontent',
                        'sections' => array(42),
                        'preview' => array(
                            'template' => 'template.html.twig',
                        ),
                    ),
                ),
            )
        );

        $this->assertContainerBuilderHasParameter(
            'netgen_content_browser.item_types',
            array(
                'ezcontent' => 'item_types.ezcontent',
            )
        );

        $this->assertContainerBuilderHasService('netgen_content_browser.item_repository');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.ezlocation');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.eztags');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.sylius_product');
        $this->assertContainerBuilderHasService('netgen_content_browser.config.ezcontent');

        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_config');
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

        $this->assertContainerBuilderHasService('netgen_content_browser.item_repository');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.ezlocation');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.eztags');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.sylius_product');

        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_config');
    }
}
