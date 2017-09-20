<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusProduct;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId;
use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

class ProductIdTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId
     */
    private $provider;

    public function setUp()
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

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
