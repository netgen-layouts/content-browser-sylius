<?php

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Backend\Sylius\ProductRepositoryInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Item\Sylius\Product\Location;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

class SyliusProductBackend implements BackendInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * @var \Netgen\ContentBrowser\Backend\Sylius\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface
     */
    protected $localeContext;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface $taxonRepository
     * @param \Netgen\ContentBrowser\Backend\Sylius\ProductRepositoryInterface $productRepository
     * @param \Sylius\Component\Locale\Context\LocaleContextInterface $localeContext
     */
    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductRepositoryInterface $productRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
        $this->localeContext = $localeContext;
    }

    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getDefaultSections()
    {
        return $this->buildLocations(
            $this->taxonRepository->findRootNodes()
        );
    }

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface
     */
    public function loadLocation($id)
    {
        $taxon = $this->taxonRepository->find($id);

        if (!$taxon instanceof TaxonInterface) {
            throw new NotFoundException(
                sprintf(
                    'Location with ID %s not found.',
                    $id
                )
            );
        }

        return $this->buildLocation($taxon);
    }

    /**
     * Loads the item by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    public function loadItem($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundException(
                sprintf(
                    'Item with ID %s not found.',
                    $id
                )
            );
        }

        return $this->buildItem($product);
    }

    /**
     * Returns the locations below provided location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location)
    {
        $taxons = $this->taxonRepository->findBy(
            array(
                'parent' => $location->getTaxon(),
            )
        );

        return $this->buildLocations($taxons);
    }

    /**
     * Returns the count of locations below provided location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location)
    {
        return count($this->getSubLocations($location));
    }

    /**
     * Returns the location items.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        $paginator = $this->productRepository->createByTaxonPaginator(
            $location->getTaxon(),
            $this->localeContext->getLocaleCode()
        );

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int) ($offset / $limit) + 1);

        return $this->buildItems(
            $paginator->getCurrentPageResults()
        );
    }

    /**
     * Returns the location items count.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location)
    {
        $paginator = $this->productRepository->createByTaxonPaginator(
            $location->getTaxon(),
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    public function search($searchText, $offset = 0, $limit = 25)
    {
        $paginator = $this->productRepository->createSearchPaginator(
            $searchText,
            $this->localeContext->getLocaleCode()
        );

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int) ($offset / $limit) + 1);

        return $this->buildItems(
            $paginator->getCurrentPageResults()
        );
    }

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     *
     * @return int
     */
    public function searchCount($searchText)
    {
        $paginator = $this->productRepository->createSearchPaginator(
            $searchText,
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    /**
     * Builds the location from provided taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Product\Location
     */
    protected function buildLocation(TaxonInterface $taxon)
    {
        return new Location($taxon);
    }

    /**
     * Builds the locations from provided taxons.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Product\Location[]
     */
    protected function buildLocations(array $taxons)
    {
        return array_map(
            function (TaxonInterface $taxon) {
                return $this->buildLocation($taxon);
            },
            $taxons
        );
    }

    /**
     * Builds the item from provided product.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface $product
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Product\Item
     */
    protected function buildItem(ProductInterface $product)
    {
        return new Item($product);
    }

    /**
     * Builds the items from provided products.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface[]|\Iterator $products
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Product\Item[]
     */
    protected function buildItems($products)
    {
        $items = array();

        foreach ($products as $product) {
            $items[] = $this->buildItem($product);
        }

        return $items;
    }
}
