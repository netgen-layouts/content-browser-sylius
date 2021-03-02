<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\ColumnProvider\ColumnValueProvider\Product;

use Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Product\ProductId;
use Netgen\ContentBrowser\Sylius\Item\Product\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Product;
use PHPUnit\Framework\TestCase;

final class ProductIdTest extends TestCase
{
    private ProductId $provider;

    protected function setUp(): void
    {
        $this->provider = new ProductId();
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Product\ProductId::getValue
     */
    public function testGetValue(): void
    {
        $product = new Product();
        $product->setId(42);

        $item = new Item($product);

        self::assertSame('42', $this->provider->getValue($item));
    }
}
