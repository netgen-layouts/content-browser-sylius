<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Backend\Sylius\TaxonRepositoryInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Item\Sylius\Taxon\TaxonInterface as ContentBrowserTaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class SyliusTaxonBackend implements BackendInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\Sylius\TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface
     */
    private $localeContext;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
    }

    public function getDefaultSections()
    {
        return $this->buildItems(
            $this->taxonRepository->findRootNodes()
        );
    }

    public function loadLocation($id)
    {
        return $this->loadItem($id);
    }

    public function loadItem($id)
    {
        $taxon = $this->taxonRepository->find($id);

        if (!$taxon instanceof TaxonInterface) {
            throw new NotFoundException(
                sprintf(
                    'Item with ID %s not found.',
                    $id
                )
            );
        }

        return $this->buildItem($taxon);
    }

    public function getSubLocations(LocationInterface $location)
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return [];
        }

        $taxons = $this->taxonRepository->findChildren(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode()
        );

        return $this->buildItems($taxons);
    }

    public function getSubLocationsCount(LocationInterface $location)
    {
        return count($this->getSubLocations($location));
    }

    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return [];
        }

        $paginator = $this->taxonRepository->createListPaginator(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode()
        );

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int) ($offset / $limit) + 1);

        return $this->buildItems($paginator->getCurrentPageResults());
    }

    public function getSubItemsCount(LocationInterface $location)
    {
        if (!$location instanceof ContentBrowserTaxonInterface) {
            return 0;
        }

        $paginator = $this->taxonRepository->createListPaginator(
            (string) $location->getTaxon()->getCode(),
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    public function search($searchText, $offset = 0, $limit = 25)
    {
        $paginator = $this->taxonRepository->createSearchPaginator(
            $searchText,
            $this->localeContext->getLocaleCode()
        );

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int) ($offset / $limit) + 1);

        return $this->buildItems(
            $paginator->getCurrentPageResults()
        );
    }

    public function searchCount($searchText)
    {
        $paginator = $this->taxonRepository->createSearchPaginator(
            $searchText,
            $this->localeContext->getLocaleCode()
        );

        return $paginator->getNbResults();
    }

    /**
     * Builds the item from provided taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Taxon\Item
     */
    private function buildItem(TaxonInterface $taxon)
    {
        return new Item($taxon);
    }

    /**
     * Builds the items from provided products.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons
     *
     * @return \Netgen\ContentBrowser\Item\Sylius\Taxon\Item[]
     */
    private function buildItems($taxons)
    {
        $items = [];

        foreach ($taxons as $taxon) {
            $items[] = $this->buildItem($taxon);
        }

        return $items;
    }
}
