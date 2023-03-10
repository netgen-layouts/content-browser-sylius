<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\ColumnProvider\ColumnValueProvider\Product;

use Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Product\ProductId;
use Netgen\ContentBrowser\Sylius\Item\Product\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Item as StubItem;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductId::class)]
final class ProductIdTest extends TestCase
{
    private ProductId $provider;

    protected function setUp(): void
    {
        $this->provider = new ProductId();
    }

    public function testGetValue(): void
    {
        $product = new Product();
        $product->setId(42);

        $item = new Item($product);

        self::assertSame('42', $this->provider->getValue($item));
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem('value')));
    }
}
