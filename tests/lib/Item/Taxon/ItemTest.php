<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\Taxon;

use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

#[CoversClass(Item::class)]
final class ItemTest extends TestCase
{
    private TaxonInterface $taxon;

    private Item $item;

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

    public function testGetLocationId(): void
    {
        self::assertSame(42, $this->item->getLocationId());
    }

    public function testGetValue(): void
    {
        self::assertSame(42, $this->item->getValue());
    }

    public function testGetName(): void
    {
        self::assertSame('Some name', $this->item->getName());
    }

    public function testGetParentId(): void
    {
        self::assertSame(24, $this->item->getParentId());
    }

    public function testGetParentIdWithNoParentTaxon(): void
    {
        $this->item = new Item(new Taxon());

        self::assertNull($this->item->getParentId());
    }

    public function testIsVisible(): void
    {
        self::assertTrue($this->item->isVisible());
    }

    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable());
    }

    public function testGetTaxon(): void
    {
        self::assertSame($this->taxon, $this->item->getTaxon());
    }
}
