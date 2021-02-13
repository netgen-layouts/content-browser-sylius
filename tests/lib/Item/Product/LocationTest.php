<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\Product;

use Netgen\ContentBrowser\Sylius\Item\Product\Location;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class LocationTest extends TestCase
{
    private TaxonInterface $taxon;

    private TaxonInterface $parentTaxon;

    private Location $location;

    protected function setUp(): void
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
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::__construct
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::getLocationId
     */
    public function testGetLocationId(): void
    {
        self::assertSame(42, $this->location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Some name', $this->location->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::getParentId
     */
    public function testGetParentId(): void
    {
        self::assertSame(24, $this->location->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::getParentId
     */
    public function testGetParentIdWithNoParentTaxon(): void
    {
        $this->location = new Location(new Taxon());

        self::assertNull($this->location->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Product\Location::getTaxon
     */
    public function testGetProduct(): void
    {
        self::assertSame($this->taxon, $this->location->getTaxon());
    }
}
