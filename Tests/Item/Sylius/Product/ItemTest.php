<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Product;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Taxon;
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
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getType
     */
    public function testGetType()
    {
        $this->assertEquals('sylius_product', $this->item->getType());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Some name', $this->item->getName());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getParentId
     */
    public function testGetParentId()
    {
        $this->assertEquals(24, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getParentId
     */
    public function testGetParentIdWithNoTaxon()
    {
        $this->item = new Item(new Product(42));

        $this->assertNull($this->item->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::isVisible
     */
    public function testIsVisible()
    {
        $this->assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item::getProduct
     */
    public function testGetProduct()
    {
        $this->assertEquals($this->product, $this->item->getProduct());
    }
}
