<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\Product;

use Netgen\ContentBrowser\Sylius\Item\Product\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductInterface;

#[CoversClass(Item::class)]
final class ItemTest extends TestCase
{
    private ProductInterface $product;

    private Item $item;

    protected function setUp(): void
    {
        $this->product = new Product();
        $this->product->setId(42);
        $this->product->setCurrentLocale('en');
        $this->product->setFallbackLocale('en');
        $this->product->setName('Some name');

        $this->item = new Item($this->product);
    }

    public function testGetValue(): void
    {
        self::assertSame(42, $this->item->getValue());
    }

    public function testGetName(): void
    {
        self::assertSame('Some name', $this->item->getName());
    }

    public function testIsVisible(): void
    {
        self::assertTrue($this->item->isVisible());
    }

    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable());
    }

    public function testGetProduct(): void
    {
        self::assertSame($this->product, $this->item->getProduct());
    }
}
