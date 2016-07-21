<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Taxon;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    protected $taxon;

    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    protected $parentTaxon;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location
     */
    protected $location;

    public function setUp()
    {
        $this->taxon = new Taxon();
        $this->taxon->setId(42);
        $this->taxon->setCurrentLocale('en');
        $this->taxon->setFallbackLocale('en');
        $this->taxon->setName('Some name');

        $this->parentTaxon = new Taxon();
        $this->parentTaxon->setId(24);
        $this->taxon->setParent($this->parentTaxon);

        $this->location = new Location($this->taxon);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getId
     */
    public function testGetId()
    {
        $this->assertEquals(42, $this->location->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getType
     */
    public function testGetType()
    {
        $this->assertEquals('sylius_product', $this->location->getType());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Some name', $this->location->getName());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getParentId
     */
    public function testGetParentId()
    {
        $this->assertEquals(24, $this->location->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getParentId
     */
    public function testGetParentIdWithNoParentTaxon()
    {
        $this->location = new Location(new Taxon());

        $this->assertNull($this->location->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location::getTaxon
     */
    public function testGetProduct()
    {
        $this->assertEquals($this->taxon, $this->location->getTaxon());
    }
}
