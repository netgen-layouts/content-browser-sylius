<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\Product;

use Netgen\ContentBrowser\Sylius\Item\Product\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Product;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    private $product;

    /**
     * @var \Netgen\ContentBrowser\Sylius\Item\Product\Item
     */
    private $item;

    public function setUp(): void
    {
        $this->product = new Product();
        $this->product->setId(42);
        $this->product->setCurrentLocale('en');
        $this->product->setFallbackLocale('en');
        $this->product->setName('Some name');

        $this->item = new Item($this->product);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::__construct
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Some name', $this->item->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::isVisible
     */
    public function testIsVisible(): void
    {
        self::assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::isSelectable
     */
    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Item::getProduct
     */
    public function testGetProduct(): void
    {
        self::assertSame($this->product, $this->item->getProduct());
    }
}
