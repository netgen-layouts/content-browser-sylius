<?php

namespace Netgen\ContentBrowser\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\Sylius\TaxonRepositoryInterface;
use Netgen\ContentBrowser\Backend\SyliusTaxonBackend;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\Kernel;

class SyliusTaxonBackendTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxonRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeContextMock;

    /**
     * @var \Netgen\ContentBrowser\Backend\SyliusTaxonBackend
     */
    protected $backend;

    public function setUp()
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
    public function testGetDefaultSections()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findRootNodes')
            ->will($this->returnValue(array($this->getTaxon(1), $this->getTaxon(2))));

        $locations = $this->backend->getDefaultSections();

        $this->assertCount(2, $locations);

        foreach ($locations as $location) {
            $this->assertInstanceOf(LocationInterface::class, $location);
        }

        $this->assertEquals(1, $locations[0]->getLocationId());
        $this->assertEquals(2, $locations[1]->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadLocation
     */
    public function testLoadLocation()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->getTaxon(1)));

        $location = $this->backend->loadLocation(1);

        $this->assertInstanceOf(LocationInterface::class, $location);
        $this->assertEquals(1, $location->getLocationId());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadLocation
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
     */
    public function testLoadLocationThrowsNotFoundException()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadLocation(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     */
    public function testLoadItem()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->getTaxon(1)));

        $item = $this->backend->loadItem(1);

        $this->assertInstanceOf(ItemInterface::class, $item);
        $this->assertEquals(1, $item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubLocations
     */
    public function testGetSubLocations()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findChildren')
            ->with(
                $this->equalTo('code'),
                $this->equalTo('en')
            )
            ->will($this->returnValue(array($this->getTaxon(2, 1), $this->getTaxon(3, 1))));

        $locations = $this->backend->getSubLocations(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertCount(2, $locations);
        foreach ($locations as $location) {
            $this->assertInstanceOf(LocationInterface::class, $location);
            $this->assertEquals(1, $location->getParentId());
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findChildren')
            ->with(
                $this->equalTo('code'),
                $this->equalTo('en')
            )
            ->will($this->returnValue(array($this->getTaxon(2), $this->getTaxon(3))));

        $count = $this->backend->getSubLocationsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     */
    public function testGetSubItems()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(0), $this->equalTo(25))
            ->will($this->returnValue(new ArrayIterator(array($this->getTaxon(), $this->getTaxon()))));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->equalTo('code'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     */
    public function testGetSubItemsWithOffsetAndLimit()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(15));

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(8), $this->equalTo(2))
            ->will($this->returnValue(new ArrayIterator(array($this->getTaxon(), $this->getTaxon()))));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->equalTo('code'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Item($this->getTaxon(1, null, 'code')),
            8,
            2
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::getSubItemsCount
     */
    public function testGetSubItemsCount()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createListPaginator')
            ->with($this->equalTo('code'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Item($this->getTaxon(1, null, 'code'))
        );

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::search
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     */
    public function testSearch()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(0), $this->equalTo(25))
            ->will($this->returnValue(new ArrayIterator(array($this->getTaxon(), $this->getTaxon()))));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->equalTo('test'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test');

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::search
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::buildItems
     */
    public function testSearchWithOffsetAndLimit()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(15));

        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(8), $this->equalTo(2))
            ->will($this->returnValue(new ArrayIterator(array($this->getTaxon(), $this->getTaxon()))));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->equalTo('test'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test', 8, 2);

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusTaxonBackend::searchCount
     */
    public function testSearchCount()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('createSearchPaginator')
            ->with($this->equalTo('test'), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->searchCount('test');

        $this->assertEquals(2, $count);
    }

    /**
     * Returns the taxon object used in tests.
     *
     * @param int $id
     * @param int $parentId
     * @param string $code
     *
     * @return \Sylius\Component\Taxonomy\Model\Taxon
     */
    protected function getTaxon($id = null, $parentId = null, $code = null)
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
