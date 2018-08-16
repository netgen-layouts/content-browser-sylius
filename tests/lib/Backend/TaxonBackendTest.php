<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Sylius\Backend\TaxonBackend;
use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Repository\TaxonRepositoryInterface;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Location as StubLocation;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class TaxonBackendTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Sylius\Repository\TaxonRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxonRepositoryMock;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $localeContextMock;

    /**
     * @var \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend
     */
    private $backend;

    public function setUp(): void
    {
        $this->taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        $this->localeContextMock = $this->createMock(LocaleContextInterface::class);

        $this->localeContextMock
            ->expects(self::any())
            ->method('getLocaleCode')
            ->will(self::returnValue('en'));

        $this->backend = new TaxonBackend(
            $this->taxonRepositoryMock,
            $this->localeContextMock
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::__construct
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSections
     */
    public function testGetSections(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findRootNodes')
            ->will(self::returnValue([$this->getTaxon(1), $this->getTaxon(2)]));

        $locations = $this->backend->getSections();

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);

        self::assertSame(1, $locations[0]->getLocationId());
        self::assertSame(2, $locations[1]->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::loadLocation
     */
    public function testLoadLocation(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->will(self::returnValue($this->getTaxon(1)));

        $location = $this->backend->loadLocation(1);

        self::assertInstanceOf(Item::class, $location);
        self::assertSame(1, $location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::loadLocation
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with value "1" not found.
     */
    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->will(self::returnValue(null));

        $this->backend->loadLocation(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::loadItem
     */
    public function testLoadItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->will(self::returnValue($this->getTaxon(1)));

        $item = $this->backend->loadItem(1);

        self::assertInstanceOf(Item::class, $item);
        self::assertSame(1, $item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with value "1" not found.
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->will(self::returnValue(null));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findChildren')
            ->with(
                self::identicalTo('code'),
                self::identicalTo('en')
            )
            ->will(self::returnValue([$this->getTaxon(2, 1), $this->getTaxon(3, 1)]));

        $locations = $this->backend->getSubLocations(
            new Item($this->getTaxon(1, null, 'code'))
        );

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);

        foreach ($locations as $location) {
            self::assertSame(1, $location->getParentId());
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubLocations
     */
    public function testGetSubLocationsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('findChildren');

        $locations = $this->backend->getSubLocations(new StubLocation(0));

        self::assertSame([], $locations);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findChildren')
            ->with(
                self::identicalTo('code'),
                self::identicalTo('en')
            )
            ->will(self::returnValue([$this->getTaxon(2), $this->getTaxon(3)]));

        $count = $this->backend->getSubLocationsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        self::assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubItems
     */
    public function testGetSubItems(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->will(self::returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code'))
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubItems
     */
    public function testGetSubItemsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('createListPaginator');

        $items = $this->backend->getSubItems(new StubLocation(0));

        self::assertSame([], $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubItems
     */
    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getNbResults')
            ->will(self::returnValue(15));

        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->will(self::returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
            8,
            2
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubItemsCount
     */
    public function testGetSubItemsCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getNbResults')
            ->will(self::returnValue(2));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        self::assertSame(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::getSubItemsCount
     */
    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('createListPaginator');

        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        self::assertSame(0, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::search
     */
    public function testSearch(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->will(self::returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test');

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::buildItems
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::search
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getNbResults')
            ->will(self::returnValue(15));

        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->will(self::returnValue(new ArrayIterator([$this->getTaxon(), $this->getTaxon()])));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test', 8, 2);

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Sylius\Backend\TaxonBackend::searchCount
     */
    public function testSearchCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects(self::any())
            ->method('getNbResults')
            ->will(self::returnValue(2));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->will(self::returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->searchCount('test');

        self::assertSame(2, $count);
    }

    /**
     * Returns the taxon object used in tests.
     *
     * @param int|string $id
     * @param int|string $parentId
     * @param string $code
     *
     * @return \Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon
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