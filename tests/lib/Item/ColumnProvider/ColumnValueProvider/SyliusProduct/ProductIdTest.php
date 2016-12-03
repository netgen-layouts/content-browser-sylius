<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusProduct;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId;
use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new ProductId();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId::getValue
     */
    public function testGetValue()
    {
        $product = new Product();
        $product->setId(42);

        $item = new Item($product);

        $this->assertEquals(
            42,
            $this->provider->getValue($item)
        );
    }
}
