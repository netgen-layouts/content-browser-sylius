<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend\Sylius;

use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

interface TaxonRepositoryInterface extends BaseTaxonRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter taxons.
     *
     * @param string $parentCode
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createListPaginator($parentCode, $localeCode);

    /**
     * Creates a paginator which is used to search for taxons.
     *
     * @param string $searchText
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createSearchPaginator($searchText, $localeCode);
}
