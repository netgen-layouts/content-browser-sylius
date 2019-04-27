<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\Taxon;

use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $taxon;

    /**
     * @var \Netgen\ContentBrowser\Sylius\Item\Taxon\Item
     */
    private $item;

    protected function setUp(): void
    {
        $parentTaxon = new Taxon();
        $parentTaxon->setId(24);

        $this->taxon = new Taxon();
        $this->taxon->setId(42);
        $this->taxon->setCurrentLocale('en');
        $this->taxon->setFallbackLocale('en');
        $this->taxon->setName('Some name');
        $this->taxon->setParent($parentTaxon);

        $this->item = new Item($this->taxon);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::__construct
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getLocationId
     */
    public function testGetLocationId(): void
    {
        self::assertSame(42, $this->item->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Some name', $this->item->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getParentId
     */
    public function testGetParentId(): void
    {
        self::assertSame(24, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getParentId
     */
    public function testGetParentIdWithNoParentTaxon(): void
    {
        $this->item = new Item(new Taxon());

        self::assertNull($this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::isVisible
     */
    public function testIsVisible(): void
    {
        self::assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::isSelectable
     */
    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Item\Taxon\Item::getTaxon
     */
    public function testGetTaxon(): void
    {
        self::assertSame($this->taxon, $this->item->getTaxon());
    }
}
