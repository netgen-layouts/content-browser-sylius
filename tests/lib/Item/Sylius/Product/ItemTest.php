<?php

namespace Netgen\ContentBrowser\Tests\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    protected $product;

    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    protected $taxon;

    /**
     * @var \Netgen\ContentBrowser\Item\Sylius\Product\Item
     */
    protected $item;

    public function setUp()
    {
        $this->product = new Product();
        $this->product->setId(42);
        $this->product->setCurrentLocale('en');
        $this->product->setFallbackLocale('en');
        $this->product->setName('Some name');

        $this->taxon = new Taxon();
        $this->taxon->setId(24);

        $this->item = new Item($this->product, $this->taxon);
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
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getParentId
     */
    public function testGetParentId()
    {
        $this->assertEquals(24, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getParentId
     */
    public function testGetParentIdWithNoTaxon()
    {
        $this->item = new Item(new Product());

        $this->assertNull($this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::isVisible
     */
    public function testIsVisible()
    {
        $this->assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Item::getProduct
     */
    public function testGetProduct()
    {
        $this->assertEquals($this->product, $this->item->getProduct());
    }
}
