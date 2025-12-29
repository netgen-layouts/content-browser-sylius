<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Sylius\Backend\TaxonBackend;
use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Service\TaxonServiceInterface;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Location as StubLocation;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

#[CoversClass(TaxonBackend::class)]
final class TaxonBackendTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface<\Sylius\Component\Taxonomy\Model\TaxonInterface>
     */
    private Stub&TaxonRepositoryInterface $taxonRepositoryStub;

    private Stub&TaxonServiceInterface $taxonServiceStub;

    private TaxonBackend $backend;

    protected function setUp(): void
    {
        $this->taxonRepositoryStub = self::createStub(TaxonRepositoryInterface::class);
        $this->taxonServiceStub = self::createStub(TaxonServiceInterface::class);

        $localeContextStub = self::createStub(LocaleContextInterface::class);

        $localeContextStub
            ->method('getLocaleCode')
            ->willReturn('en');

        $this->backend = new TaxonBackend(
            $this->taxonRepositoryStub,
            $this->taxonServiceStub,
            $localeContextStub,
        );
    }

    public function testGetSections(): void
    {
        $this->taxonRepositoryStub
            ->method('findRootNodes')
            ->willReturn([$this->getTaxon(1), $this->getTaxon(2)]);

        $locations = $this->backend->getSections();

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);
    }

    public function testLoadLocation(): void
    {
        $this->taxonRepositoryStub
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn($this->getTaxon(1));

        $location = $this->backend->loadLocation(1);

        self::assertSame(1, $location->locationId);
    }

    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->taxonRepositoryStub
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn(null);

        $this->backend->loadLocation(1);
    }

    public function testLoadItem(): void
    {
        $this->taxonRepositoryStub
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn($this->getTaxon(1));

        $item = $this->backend->loadItem(1);

        self::assertSame(1, $item->value);
    }

    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->taxonRepositoryStub
            ->method('find')
            ->with(self::identicalTo(1))
            ->willReturn(null);

        $this->backend->loadItem(1);
    }

    public function testGetSubLocations(): void
    {
        $this->taxonRepositoryStub
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
            self::assertSame(1, $location->parentId);
        }
    }

    public function testGetSubLocationsWithInvalidItem(): void
    {
        $locations = $this->backend->getSubLocations(new StubLocation(0));

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubLocationsCount(): void
    {
        $this->taxonRepositoryStub
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
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);
        $pagerfantaAdapterStub
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonServiceStub
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);
    }

    public function testGetSubItemsWithInvalidItem(): void
    {
        $items = $this->backend->getSubItems(new StubLocation(0));

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);

        $pagerfantaAdapterStub
            ->method('getNbResults')
            ->willReturn(15);

        $pagerfantaAdapterStub
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonServiceStub
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

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
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);
        $pagerfantaAdapterStub
            ->method('getNbResults')
            ->willReturn(2);

        $this->taxonServiceStub
            ->method('createListPaginator')
            ->with(self::identicalTo('code'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

        $count = $this->backend->getSubItemsCount(
            new Item($this->getTaxon(1, null, 'code')),
        );

        self::assertSame(2, $count);
    }

    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        self::assertSame(0, $count);
    }

    public function testSearchItems(): void
    {
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);
        $pagerfantaAdapterStub
            ->method('getSlice')
            ->with(self::identicalTo(0), self::identicalTo(25))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonServiceStub
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

        $searchResult = $this->backend->searchItems(new SearchQuery('test'));

        self::assertCount(2, $searchResult->results);
        self::assertContainsOnlyInstancesOf(Item::class, $searchResult->results);
    }

    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);

        $pagerfantaAdapterStub
            ->method('getNbResults')
            ->willReturn(15);

        $pagerfantaAdapterStub
            ->method('getSlice')
            ->with(self::identicalTo(8), self::identicalTo(2))
            ->willReturn(new ArrayIterator([$this->getTaxon(), $this->getTaxon()]));

        $this->taxonServiceStub
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

        $searchQuery = new SearchQuery('test');
        $searchQuery->offset = 8;
        $searchQuery->limit = 2;

        $searchResult = $this->backend->searchItems($searchQuery);

        self::assertCount(2, $searchResult->results);
        self::assertContainsOnlyInstancesOf(Item::class, $searchResult->results);
    }

    public function testSearchItemsCount(): void
    {
        $pagerfantaAdapterStub = self::createStub(AdapterInterface::class);
        $pagerfantaAdapterStub
            ->method('getNbResults')
            ->willReturn(2);

        $this->taxonServiceStub
            ->method('createSearchPaginator')
            ->with(self::identicalTo('test'), self::identicalTo('en'))
            ->willReturn(new Pagerfanta($pagerfantaAdapterStub));

        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(2, $count);
    }

    /**
     * Returns the taxon object used in tests.
     */
    private function getTaxon(?int $id = null, ?int $parentId = null, ?string $code = null): Taxon
    {
        $taxon = new Taxon();
        $taxon->id = $id;

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
