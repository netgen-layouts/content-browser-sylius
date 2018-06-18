<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Sylius\Taxon;

use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class ItemTest extends TestCase
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $taxon;

    /**
     * @var \Netgen\ContentBrowser\Item\Sylius\Taxon\Item
     */
    private $item;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

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
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::__construct
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getLocationId
     */
    public function testGetLocationId(): void
    {
        $this->assertSame(42, $this->item->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getValue
     */
    public function testGetValue(): void
    {
        $this->assertSame(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Some name', $this->item->getName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getParentId
     */
    public function testGetParentId(): void
    {
        $this->assertSame(24, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getParentId
     */
    public function testGetParentIdWithNoParentTaxon(): void
    {
        $this->item = new Item(new Taxon());

        $this->assertNull($this->item->getParentId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::isVisible
     */
    public function testIsVisible(): void
    {
        $this->assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::isSelectable
     */
    public function testIsSelectable(): void
    {
        $this->assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Sylius\Taxon\Item::getTaxon
     */
    public function testGetTaxon(): void
    {
        $this->assertSame($this->taxon, $this->item->getTaxon());
    }
}
