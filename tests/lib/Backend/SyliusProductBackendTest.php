<?php

namespace Netgen\ContentBrowser\Tests\Backend;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\Sylius\ProductRepositoryInterface;
use Netgen\ContentBrowser\Backend\SyliusProductBackend;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
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

class SyliusProductBackendTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxonRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $localeContextMock;

    /**
     * @var \Netgen\ContentBrowser\Backend\SyliusProductBackend
     */
    private $backend;

    public function setUp()
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getDefaultSections
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocations
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadLocation
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocation
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadLocation
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Location with ID 1 not found.
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     */
    public function testLoadItem()
    {
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->getProduct(1)));

        $item = $this->backend->loadItem(1);

        $this->assertInstanceOf(ItemInterface::class, $item);
        $this->assertEquals(1, $item->getValue());
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue(null));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocations
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocation
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildLocations
     */
    public function testGetSubLocations()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('parent' => $this->getTaxon(1))))
            ->will($this->returnValue(array($this->getTaxon(2, 1), $this->getTaxon(3, 1))));

        $locations = $this->backend->getSubLocations(
            new Location($this->getTaxon(1))
        );

        $this->assertCount(2, $locations);
        foreach ($locations as $location) {
            $this->assertInstanceOf(LocationInterface::class, $location);
            $this->assertEquals(1, $location->getParentId());
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocations
     */
    public function testGetSubLocationsWithInvalidItem()
    {
        $this->taxonRepositoryMock
            ->expects($this->never())
            ->method('findBy');

        $locations = $this->backend->getSubLocations(new StubLocation(0));

        $this->assertEquals(array(), $locations);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount()
    {
        $this->taxonRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('parent' => $this->getTaxon(1))))
            ->will($this->returnValue(array($this->getTaxon(2), $this->getTaxon(3))));

        $count = $this->backend->getSubLocationsCount(
            new Location($this->getTaxon(1))
        );

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     */
    public function testGetSubItems()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(0), $this->equalTo(25))
            ->will($this->returnValue(new ArrayIterator(array($this->getProduct(), $this->getProduct()))));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->equalTo($this->getTaxon(1)), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Location($this->getTaxon(1))
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     */
    public function testGetSubItemsWithInvalidItem()
    {
        $this->productRepositoryMock
            ->expects($this->never())
            ->method('createByTaxonPaginator');

        $items = $this->backend->getSubItems(new StubLocation(0));

        $this->assertEquals(array(), $items);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItems
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
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
            ->will($this->returnValue(new ArrayIterator(array($this->getProduct(), $this->getProduct()))));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->equalTo($this->getTaxon(1)), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->getSubItems(
            new Location($this->getTaxon(1)),
            8,
            2
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItemsCount
     */
    public function testGetSubItemsCount()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('createByTaxonPaginator')
            ->with($this->equalTo($this->getTaxon(1)), $this->equalTo('en'))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Location($this->getTaxon(1))
        );

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::getSubItemsCount
     */
    public function testGetSubItemsCountWithInvalidItem()
    {
        $this->productRepositoryMock
            ->expects($this->never())
            ->method('createByTaxonPaginator');

        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        $this->assertEquals(0, $count);
    }

    /**
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::search
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
     */
    public function testSearch()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo(0), $this->equalTo(25))
            ->will($this->returnValue(new ArrayIterator(array($this->getProduct(), $this->getProduct()))));

        $this->productRepositoryMock
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::search
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::buildItems
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
            ->will($this->returnValue(new ArrayIterator(array($this->getProduct(), $this->getProduct()))));

        $this->productRepositoryMock
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
     * @covers \Netgen\ContentBrowser\Backend\SyliusProductBackend::searchCount
     */
    public function testSearchCount()
    {
        $pagerfantaAdapterMock = $this->createMock(AdapterInterface::class);
        $pagerfantaAdapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(2));

        $this->productRepositoryMock
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
     *
     * @return \Sylius\Component\Taxonomy\Model\Taxon
     */
    private function getTaxon($id = null, $parentId = null)
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
     * @param int $id
     *
     * @return \Sylius\Component\Product\Model\Product
     */
    private function getProduct($id = null)
    {
        $product = new Product();
        $product->setId($id);

        return $product;
    }
}
