<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusProduct;

use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new ProductId();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId::getValue
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
