<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Backend;

use Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Taxon;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Product;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use ArrayIterator;

class SyliusProductBackendTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxonRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend
     */
    protected $backend;

    public function setUp()
    {
        $this->taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);

        $this->backend = new SyliusProductBackend(
            $this->taxonRepositoryMock,
            $this->productRepositoryMock
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getDefaultSections
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildLocations
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

        $this->assertEquals(1, $locations[0]->getId());
        $this->assertEquals(2, $locations[1]->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::loadLocation
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildLocation
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
        $this->assertEquals(1, $location->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::loadLocation
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::loadItem
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItem
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::loadItem
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getSubLocations
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildLocation
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildLocations
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getSubLocationsCount
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getSubItems
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItems
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
            ->with($this->equalTo($this->getTaxon(1)))
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getSubItems
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItems
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
            ->with($this->equalTo($this->getTaxon(1)))
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::getSubItemsCount
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
            ->with($this->equalTo($this->getTaxon(1)))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $count = $this->backend->getSubItemsCount(
            new Location($this->getTaxon(1))
        );

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::search
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItems
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
            ->method('createFilterPaginator')
            ->with($this->equalTo(array('name' => 'test')))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test');

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::search
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItem
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::buildItems
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
            ->method('createFilterPaginator')
            ->with($this->equalTo(array('name' => 'test')))
            ->will($this->returnValue(new Pagerfanta($pagerfantaAdapterMock)));

        $items = $this->backend->search('test', 8, 2);

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Backend\SyliusProductBackend::searchCount
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
            ->method('createFilterPaginator')
            ->with($this->equalTo(array('name' => 'test')))
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
    protected function getTaxon($id = null, $parentId = null)
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
    protected function getProduct($id = null)
    {
        $product = new Product();
        $product->setId($id);

        return $product;
    }
}
