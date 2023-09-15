<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Backend;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Backend\SearchResultInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Item\Taxon\TaxonInterface as ContentBrowserTaxonInterface;
use Netgen\ContentBrowser\Sylius\Repository\TaxonRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

use function count;
use function is_countable;
use function sprintf;

final class TaxonBackend implements BackendInterface
{
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext,
    ) {}

    public function getSections(): iterable
    {
        /** @var iterable<\Sylius\Component\Taxonomy\Model\TaxonInterface> $rootNodes */
        $rootNodes = $this->taxonRepository->findRootNodes();

        return $this->buildItems($rootNodes);
    }

    public function loadLocation($id): Item
    {
        return $this->internalLoadItem((int) $id);
    }

    public function loadItem($value): Item
    {
        return $this->internalLoadItem((int) $value);
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return [];
        }

        /** @var iterable<\Sylius\Component\Taxonomy\Model\TaxonInterface> $taxons */
        $taxons = $this->taxonRepository->findChildren(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode(),
        );

        return $this->buildItems($taxons);
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

        $paginator = $this->taxonRepository->createListPaginator(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode(),
        );

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int) ($offset / $limit) + 1);

        return $this->buildItems($paginator->getCurrentPageResults());
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return 0;
        }

        $paginator = $this->taxonRepository->createListPaginator(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode(),
        );

        return $paginator->getNbResults();
    }

    public function searchItems(SearchQuery $searchQuery): SearchResultInterface
    {
        $paginator = $this->taxonRepository->createSearchPaginator(
            $searchQuery->getSearchText(),
            $this->localeContext->getLocaleCode(),
        );

        $paginator->setMaxPerPage($searchQuery->getLimit());
        $paginator->setCurrentPage((int) ($searchQuery->getOffset() / $searchQuery->getLimit()) + 1);

        return new SearchResult(
            $this->buildItems(
                $paginator->getCurrentPageResults(),
            ),
        );
    }

    public function searchItemsCount(SearchQuery $searchQuery): int
    {
        $paginator = $this->taxonRepository->createSearchPaginator(
            $searchQuery->getSearchText(),
            $this->localeContext->getLocaleCode(),
        );

        return $paginator->getNbResults();
    }

    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable
    {
        $searchQuery = new SearchQuery($searchText);
        $searchQuery->setOffset($offset);
        $searchQuery->setLimit($limit);

        $searchResult = $this->searchItems($searchQuery);

        return $searchResult->getResults();
    }

    public function searchCount(string $searchText): int
    {
        return $this->searchItemsCount(new SearchQuery($searchText));
    }

    /**
     * Returns the item for provided value.
     */
    private function internalLoadItem(int $value): Item
    {
        $taxon = $this->taxonRepository->find($value);

        if (!$taxon instanceof TaxonInterface) {
            throw new NotFoundException(
                sprintf(
                    'Item with value "%s" not found.',
                    $value,
                ),
            );
        }

        return $this->buildItem($taxon);
    }

    /**
     * Builds the item from provided taxon.
     */
    private function buildItem(TaxonInterface $taxon): Item
    {
        return new Item($taxon);
    }

    /**
     * Builds the items from provided products.
     *
     * @param iterable<\Sylius\Component\Taxonomy\Model\TaxonInterface> $taxons
     *
     * @return \Netgen\ContentBrowser\Sylius\Item\Taxon\Item[]
     */
    private function buildItems(iterable $taxons): array
    {
        $items = [];

        foreach ($taxons as $taxon) {
            $items[] = $this->buildItem($taxon);
        }

        return $items;
    }
}
