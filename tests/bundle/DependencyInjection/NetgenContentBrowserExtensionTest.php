<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;

final class NetgenContentBrowserExtensionTest extends AbstractExtensionTestCase
{
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

        $this->assertContainerBuilderHasService('netgen_content_browser.backend.ezlocation');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.ezcontent');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.eztags');
        $this->assertContainerBuilderHasService('netgen_content_browser.backend.sylius_product');

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

        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.ezlocation');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.ezcontent');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.eztags');
        $this->assertContainerBuilderNotHasService('netgen_content_browser.backend.sylius_product');

        $this->assertContainerBuilderHasSyntheticService('netgen_content_browser.current_config');
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Item type must begin with a letter and be followed by any combination of letters, digits and underscore.
     */
    public function testLoadThrowsRuntimeExceptionOnInvalidItemType()
    {
        $this->container->setParameter('kernel.bundles', array());

        $this->load(
            array(
                'item_types' => array(
                    'Item type' => array(
                        'name' => 'item_types.ezcontent',
                        'preview' => array(
                            'template' => 'template.html.twig',
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * We test for existence of one config value from each of the config files.
     *
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::doPrepend
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::prepend
     */
    public function testPrepend()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'EzPublishCoreBundle' => 'EzPublishCoreBundle',
                'NetgenTagsBundle' => 'NetgenTagsBundle',
                'SyliusCoreBundle' => 'SyliusCoreBundle',
            )
        );

        $extension = $this->container->getExtension('netgen_content_browser');
        $extension->prepend($this->container);

        $config = call_user_func_array(
            'array_merge_recursive',
            $this->container->getExtensionConfig('netgen_content_browser')
        );

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('item_types', $config);

        $this->assertArrayHasKey('ezcontent', $config['item_types']);
        $this->assertArrayHasKey('ezlocation', $config['item_types']);
        $this->assertArrayHasKey('eztags', $config['item_types']);
        $this->assertArrayHasKey('sylius_product', $config['item_types']);
    }

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
}
