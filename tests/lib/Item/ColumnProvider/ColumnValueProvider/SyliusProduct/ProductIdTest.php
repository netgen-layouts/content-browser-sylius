<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusProduct;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId;
use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class ProductIdTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId
     */
    private $provider;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

        $this->provider = new ProductId();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId::getValue
     */
    public function testGetValue(): void
    {
        $product = new Product();
        $product->setId(42);

        $item = new Item($product);

        $this->assertEquals(
            '42',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct\ProductId::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        $this->assertNull($this->provider->getValue(new StubItem()));
    }
}
