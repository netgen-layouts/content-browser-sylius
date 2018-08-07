<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\Sylius\Product\Location;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class LocationTest extends TestCase
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $taxon;

    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $parentTaxon;

    /**
     * @var \Netgen\ContentBrowser\Item\Sylius\Product\Location
     */
    private $location;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            self::markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

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
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::__construct
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::getLocationId
     */
    public function testGetLocationId(): void
    {
        self::assertSame(42, $this->location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Some name', $this->location->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::getParentId
     */
    public function testGetParentId(): void
    {
        self::assertSame(24, $this->location->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::getParentId
     */
    public function testGetParentIdWithNoParentTaxon(): void
    {
        $this->location = new Location(new Taxon());

        self::assertNull($this->location->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Product\Location::getTaxon
     */
    public function testGetProduct(): void
    {
        self::assertSame($this->taxon, $this->location->getTaxon());
    }
}
