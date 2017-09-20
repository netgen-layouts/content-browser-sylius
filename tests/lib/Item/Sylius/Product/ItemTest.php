<?php

namespace Netgen\ContentBrowser\Tests\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

class ItemTest extends TestCase
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    private $product;

    /**
     * @var \Netgen\ContentBrowser\Item\Sylius\Product\Item
     */
    private $item;

    public function setUp()
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

        $this->product = new Product();
        $this->product->setId(42);
        $this->product->setCurrentLocale('en');
        $this->product->setFallbackLocale('en');
        $this->product->setName('Some name');

        $this->item = new Item($this->product);
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::__construct
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Some name', $this->item->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::isVisible
     */
    public function testIsVisible()
    {
        $this->assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::isSelectable
     */
    public function testIsSelectable()
    {
        $this->assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getProduct
     */
    public function testGetProduct()
    {
        $this->assertEquals($this->product, $this->item->getProduct());
    }
}
