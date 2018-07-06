<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\Sylius\ProductRepositoryInterface;
use Netgen\ContentBrowser\Backend\SyliusProductBackend;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Item\Sylius\Product\Location;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use Netgen\ContentBrowser\Tests\Stubs\Location as StubLocation;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpKernel\Kernel;

final class SyliusProductBackendTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $taxonRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $productRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $localeContextMock;

    /**
     * @var \Netgen\ContentBrowser\Backend\SyliusProductBackend
     */
    private $backend;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

        $this->taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->localeContextMock = $this->createMock(LocaleContextInterface::class);

        $this->localeContextMock
            ->expects($this->any())
            ->method('getLocaleCode')
            ->will($this->returnValue('en'));

        $this->backend = new SyliusProductBackend(
            $this->taxonRepositoryMock,
            $this->productRepositoryMock,
            $this->localeContextMock
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::__construct
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocations
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getDefaultSections
     */
    public function testGetDefaultSections(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findRootNodes')
            ->will($this->returnValue([$this->getTaxon(1), $this->getTaxon(2)]));

        $locations = $this->backend->getDefaultSections();

        $this->assertCount(2, $locations);

        foreach ($locations as $location) {
            $this->assertInstanceOf(Location::class, $location);
            $this->assertInstanceOf(LocationInterface::class, $location);
        }

        $this->assertSame(1, $locations[0]->getLocationId());
        $this->assertSame(2, $locations[1]->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocation
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadLocation
     */
    public function testLoadLocation(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue($this->getTaxon(1)));

        $location = $this->backend->loadLocation(1);

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(1, $location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadLocation
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Location with ID 1 not found.
     */
    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadLocation(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadItem
     */
    public function testLoadItem(): void
    {
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue($this->getProduct(1)));

        $item = $this->backend->loadItem(1);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame(1, $item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocation
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocations
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $taxon = $this->getTaxon(1);

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($this->identicalTo(['parent' => $taxon]))
            ->will($this->returnValue([$this->getTaxon(2, 1), $this->getTaxon(3, 1)]));

        $locations = $this->backend->getSubLocations(
            new Location($taxon)
        );

        $this->assertCount(2, $locations);
        foreach ($locations as $location) {
            $this->assertInstanceOf(Location::class, $location);
            $this->assertInstanceOf(LocationInterface::class, $location);
            $this->assertSame(1, $location->getParentId());
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocations
     */
    public function testGetSubLocationsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->never())
            ->method('findBy');

        $locations = $this->backend->getSubLocations(new StubLocation(0));

        $this->assertSame([], $locations);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount(): void
    {
        $taxon = $this->getTaxon(1);

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($this->identicalTo(['parent' => $taxon]))
            ->will($this->returnValue([$this->getTaxon(2), $this->getTaxon(3)]));

        $count = $this->backend->getSubLocationsCount(
            new Location($taxon)
        );

        $this->assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     */
    public function testGetSubItems(): void
    {
        $taxon = $this->getTaxon(1);

        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(0), $this->identicalTo(25))
            ->will($this->returnValue(new ArrayIterator([$this->getProduct(), $this->getProduct()])));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->identicalTo($taxon), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Location($taxon)
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     */
    public function testGetSubItemsWithInvalidItem(): void
    {
        $this->productRepositoryMock
            ->expects($this->never())
            ->method('createByTaxonPaginator');

        $items = $this->backend->getSubItems(new StubLocation(0));

        $this->assertSame([], $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     */
    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(15));

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(8), $this->identicalTo(2))
            ->will($this->returnValue(new ArrayIterator([$this->getProduct(), $this->getProduct()])));

        $taxon = $this->getTaxon(1);

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->identicalTo($taxon), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Location($taxon),
            8,
            2
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItemsCount
     */
    public function testGetSubItemsCount(): void
    {
        $taxon = $this->getTaxon(1);

        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->identicalTo($taxon), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Location($taxon)
        );

        $this->assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItemsCount
     */
    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $this->productRepositoryMock
            ->expects($this->never())
            ->method('createByTaxonPaginator');

        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        $this->assertSame(0, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::search
     */
    public function testSearch(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(0), $this->identicalTo(25))
            ->will($this->returnValue(new ArrayIterator([$this->getProduct(), $this->getProduct()])));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->identicalTo('test'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test');

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::search
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(15));

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(8), $this->identicalTo(2))
            ->will($this->returnValue(new ArrayIterator([$this->getProduct(), $this->getProduct()])));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->identicalTo('test'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test', 8, 2);

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::searchCount
     */
    public function testSearchCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->identicalTo('test'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->searchCount('test');

        $this->assertSame(2, $count);
    }

    /**
     * Returns the taxon object used in tests.
     *
     * @param int|string $id
     * @param int|string $parentId
     *
     * @return \Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon
     */
    private function getTaxon($id = null, $parentId = null): Taxon
    {
        $taxon = new Taxon();
        $taxon->setId($id);

        if ($parentId !== null) {
            $taxon->setParent(
                $this->getTaxon($parentId)
            );
        }

        return $taxon;
    }

    /**
     * Returns the product object used in tests.
     *
     * @param int|string $id
     *
     * @return \Netgen\ContentBrowser\Tests\Backend\Stubs\Product
     */
    private function getProduct($id = null): Product
    {
        $product = new Product();
        $product->setId($id);

        return $product;
    }
}
