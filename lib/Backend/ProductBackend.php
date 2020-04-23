<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Backend;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Sylius\Item\Product\Item;
use Netgen\ContentBrowser\Sylius\Item\Product\Location;
use Netgen\ContentBrowser\Sylius\Item\Product\TaxonInterface as ContentBrowserTaxonInterface;
use Netgen\ContentBrowser\Sylius\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use function array_map;
use function count;
use function is_countable;
use function sprintf;

final class ProductBackend implements BackendInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var \Netgen\ContentBrowser\Sylius\Repository\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface
     */
    private $localeContext;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductRepositoryInterface $productRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
        $this->localeContext = $localeContext;
    }

    public function getSections(): iterable
    {
        return $this->buildLocations(
            $this->taxonRepository->findRootNodes()
        );
    }

    public function loadLocation($id): LocationInterface
    {
        $taxon = $this->taxonRepository->find($id);

        if (!$taxon instanceof TaxonInterface) {
            throw new NotFoundException(
                sprintf(
                    'Location with ID "%s" not found.',
                    $id
                )
            );
        }

        return $this->buildLocation($taxon);
    }

    public function loadItem($value): ItemInterface
    {
        $product = $this->productRepository->find($value);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundException(
                sprintf(
                    'Item with value "%s" not found.',
                    $value
                )
            );
        }

        return $this->buildItem($product);
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return [];
        }

        /** @var \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons */
        $taxons = $this->taxonRepository->findBy(
            [
                'parent' => $location->getTaxon(),
            ]
        );

        return $this->buildLocations($taxons);
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
        $subLocations = $this->getSubLocations($location);

        return is_countable($subLocations) ? count($subLocations) : 0;
    }

    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return [];
        }

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

    public function getSubItemsCount(LocationInterface $location): int
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return 0;
        }

        $paginator = $this->productRepository->createByTaxonPaginator(
            $location->getTaxon(),
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable
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

    public function searchCount(string $searchText): int
    {
        $paginator = $this->productRepository->createSearchPaginator(
            $searchText,
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    /**
     * Builds the location from provided taxon.
     */
    private function buildLocation(TaxonInterface $taxon): Location
    {
        return new Location($taxon);
    }

    /**
     * Builds the locations from provided taxons.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons
     *
     * @return \Netgen\ContentBrowser\Sylius\Item\Product\Location[]
     */
    private function buildLocations(array $taxons): array
    {
        return array_map(
            function (TaxonInterface $taxon): Location {
                return $this->buildLocation($taxon);
            },
            $taxons
        );
    }

    /**
     * Builds the item from provided product.
     */
    private function buildItem(ProductInterface $product): Item
    {
        return new Item($product);
    }

    /**
     * Builds the items from provided products.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface[] $products
     *
     * @return \Netgen\ContentBrowser\Sylius\Item\Product\Item[]
     */
    private function buildItems(iterable $products): array
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = $this->buildItem($product);
        }

        return $items;
    }
}
