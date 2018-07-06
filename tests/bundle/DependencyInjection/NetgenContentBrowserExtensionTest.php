<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;

final class NetgenContentBrowserExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension
     */
    private $extension;

    public function setUp(): void
    {
        parent::setUp();

        /** @var \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension $extension */
        $extension = $this->container->getExtension('netgen_content_browser');

        $this->extension = $extension;
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension::load
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Item type must begin with a letter and be followed by any combination of letters, digits and underscore.
     */
    public function testLoadThrowsRuntimeExceptionOnInvalidItemType(): void
    {
        $this->container->setParameter('kernel.bundles', []);

        $this->load(
            [
                'item_types' => [
                    'item type' => [
                        'name' => 'item_types.item_type',
                        'preview' => [
                            'template' => 'template.html.twig',
                        ],
                    ],
                ],
            ]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenContentBrowserExtension(),
        ];
    }
}
