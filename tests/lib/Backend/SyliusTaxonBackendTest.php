<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\Sylius\TaxonRepositoryInterface;
use Netgen\ContentBrowser\Backend\SyliusTaxonBackend;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use Netgen\ContentBrowser\Tests\Stubs\Location as StubLocation;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\Kernel;

final class SyliusTaxonBackendTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Backend\Sylius\TaxonRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxonRepositoryMock;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $localeContextMock;

    /**
     * @var \Netgen\ContentBrowser\Backend\SyliusTaxonBackend
     */
    private $backend;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

        $this->taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        $this->localeContextMock = $this->createMock(LocaleContextInterface::class);

        $this->localeContextMock
            ->expects($this->any())
            ->method('getLocaleCode')
            ->will($this->returnValue('en'));

        $this->backend = new SyliusTaxonBackend(
            $this->taxonRepositoryMock,
            $this->localeContextMock
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::__construct
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getDefaultSections
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
            $this->assertInstanceOf(Item::class, $location);
        }

        $this->assertSame(1, $locations[0]->getLocationId());
        $this->assertSame(2, $locations[1]->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadLocation
     */
    public function testLoadLocation(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue($this->getTaxon(1)));

        $location = $this->backend->loadLocation(1);

        $this->assertInstanceOf(Item::class, $location);
        $this->assertSame(1, $location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadLocation
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadItem
     */
    public function testLoadItem(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue($this->getTaxon(1)));

        $item = $this->backend->loadItem(1);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame(1, $item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findChildren')
            ->with(
                $this->identicalTo('code'),
                $this->identicalTo('en')
            )
            ->will($this->returnValue([$this->getTaxon(2, 1), $this->getTaxon(3, 1)]));

        $locations = $this->backend->getSubLocations(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertCount(2, $locations);
        foreach ($locations as $location) {
            $this->assertInstanceOf(Item::class, $location);
            $this->assertInstanceOf(LocationInterface::class, $location);
            $this->assertSame(1, $location->getParentId());
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubLocations
     */
    public function testGetSubLocationsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->never())
            ->method('findChildren');

        $locations = $this->backend->getSubLocations(new StubLocation(0));

        $this->assertSame([], $locations);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findChildren')
            ->with(
                $this->identicalTo('code'),
                $this->identicalTo('en')
            )
            ->will($this->returnValue([$this->getTaxon(2), $this->getTaxon(3)]));

        $count = $this->backend->getSubLocationsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItems
     */
    public function testGetSubItems(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(0), $this->identicalTo(25))
            ->will($this->returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->identicalTo('code'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItems
     */
    public function testGetSubItemsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->never())
            ->method('createListPaginator');

        $items = $this->backend->getSubItems(new StubLocation(0));

        $this->assertSame([], $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItems
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
            ->will($this->returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->identicalTo('code'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItemsCount
     */
    public function testGetSubItemsCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->identicalTo('code'), $this->identicalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItemsCount
     */
    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects($this->never())
            ->method('createListPaginator');

        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        $this->assertSame(0, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::search
     */
    public function testSearch(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->identicalTo(0), $this->identicalTo(25))
            ->will($this->returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::search
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
            ->will($this->returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::searchCount
     */
    public function testSearchCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->taxonRepositoryMock
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
     * @param string $code
     *
     * @return \Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon
     */
    private function getTaxon($id = null, $parentId = null, ?string $code = null): Taxon
    {
        $taxon = new Taxon();
        $taxon->setId($id);

        if ($parentId !== null) {
            $taxon->setParent(
                $this->getTaxon($parentId)
            );
        }

        if ($code !== null) {
            $taxon->setCode($code);
        }

        return $taxon;
    }
}
