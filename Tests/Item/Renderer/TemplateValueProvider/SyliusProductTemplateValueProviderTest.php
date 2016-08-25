<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Renderer\TemplateValueProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class SyliusProductTemplateValueProviderTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider
     */
    protected $valueProvider;

    public function setUp()
    {
        $this->valueProvider = new SyliusProductTemplateValueProvider();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider::getValues
     */
    public function testGetValues()
    {
        $item = $this->getItem();

        $this->assertEquals(
            array(
                'product' => $item->getProduct(),
            ),
            $this->valueProvider->getValues($item)
        );
    }

    /**
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Product());
    }
}
