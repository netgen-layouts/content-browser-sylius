<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Sylius\Backend\TaxonBackend;
use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Repository\TaxonRepositoryInterface;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Location as StubLocation;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;

#[CoversClass(TaxonBackend::class)]
final class TaxonBackendTest extends TestCase
{
    private MockObject&TaxonRepositoryInterface $taxonRepositoryMock;

    private MockObject&LocaleContextInterface $localeContextMock;

    private TaxonBackend $backend;

    protected function setUp(): void
    {
        $this->taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        $this->localeContextMock = $this->createMock(LocaleContextInterface::class);

        $this->localeContextMock
            ->method('getLocaleCode')
            ->willReturn('en');

        $this->backend = new TaxonBackend(
            $this->taxonRepositoryMock,
            $this->localeContextMock,
        );
    }

    public function testGetSections(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findRootNodes')
            ->willReturn([$this->getTaxon(1), $this->getTaxon(2)]);

        $locations = $this->backend->getSections();

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);
    }

    public function testLoadLocation(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn($this->getTaxon(1));

        $location = $this->backend->loadLocation(1);

        self::assertSame(1, $location->getLocationId());
    }

    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn(null);

        $this->backend->loadLocation(1);
    }

    public function testLoadItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn($this->getTaxon(1));

        $item = $this->backend->loadItem(1);

        self::assertSame(1, $item->getValue());
    }

    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn(null);

        $this->backend->loadItem(1);
    }

    public function testGetSubLocations(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findChildren')
            ->with(
                self::identicalTo('code'),
                self::identicalTo('en'),
            )
            ->willReturn([$this->getTaxon(2, 1), $this->getTaxon(3, 1)]);

        $locations = $this->backend->getSubLocations(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);

        foreach ($locations as $location) {
            self::assertSame(1, $location->getParentId());
        }
    }

    public function testGetSubLocationsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('findChildren');

        $locations = $this->backend->getSubLocations(new StubLocation(0));

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubLocationsCount(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('findChildren')
            ->with(
                self::identicalTo('code'),
                self::identicalTo('en'),
            )
            ->willReturn([$this->getTaxon(2), $this->getTaxon(3)]);

        $count = $this->backend->getSubLocationsCount(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertSame(2, $count);
    }

    public function testGetSubItems(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    public function testGetSubItemsWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('createListPaginator');

        $items = $this->backend->getSubItems(new StubLocation(0));

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(15);

        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
            8,
            2,
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    public function testGetSubItemsCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(2);

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $count = $this->backend->getSubItemsCount(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertSame(2, $count);
    }

    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $this->taxonRepositoryMock
            ->expects(self::never())
            ->method('createListPaginator');

        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        self::assertSame(0, $count);
    }

    public function testSearchItems(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $searchResult = $this->backend->searchItems(new SearchQuery('test'));

        self::assertCount(2, $searchResult->getResults());
        self::assertContainsOnlyInstancesOf(Item::class, $searchResult->getResults());
    }

    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(15);

        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $searchQuery = new SearchQuery('test');
        $searchQuery->setOffset(8);
        $searchQuery->setLimit(2);

        $searchResult = $this->backend->searchItems($searchQuery);

        self::assertCount(2, $searchResult->getResults());
        self::assertContainsOnlyInstancesOf(Item::class, $searchResult->getResults());
    }

    public function testSearchItemsCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(2);

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(2, $count);
    }

    public function testSearch(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $items = $this->backend->search('test');

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    public function testSearchWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(15);

        $pagerfantaAdapterMock
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $items = $this->backend->search('test', 8, 2);

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    public function testSearchCount(): void
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->method('getNbResults')
            ->willReturn(2);

        $this->taxonRepositoryMock
            ->expects(self::once())
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterMock));

        $count = $this->backend->searchCount('test');

        self::assertSame(2, $count);
    }

    /**
     * Returns the taxon object used in tests.
     */
    private function getTaxon(?int $id = null, ?int $parentId = null, ?string $code = null): Taxon
    {
        $taxon = new Taxon();
        $taxon->setId($id);

        if ($parentId !== null) {
            $taxon->setParent(
                $this->getTaxon($parentId),
            );
        }

        if ($code !== null) {
            $taxon->setCode($code);
        }

        return $taxon;
    }
}
