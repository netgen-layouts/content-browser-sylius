<?php

namespace Netgen\ContentBrowser\Tests\Item\Renderer\TemplateValueProvider;

use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class SyliusProductTemplateValueProviderTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider
     */
    protected $valueProvider;

    public function setUp()
    {
        $this->valueProvider = new SyliusProductTemplateValueProvider();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Renderer\TemplateValueProvider\SyliusProductTemplateValueProvider::getValues
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
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Product());
    }
}
