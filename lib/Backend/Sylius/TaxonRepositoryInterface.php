<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend\Sylius;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

interface TaxonRepositoryInterface extends BaseTaxonRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter taxons.
     */
    public function createListPaginator(string $parentCode, string $localeCode): Pagerfanta;

    /**
     * Creates a paginator which is used to search for taxons.
     */
    public function createSearchPaginator(string $searchText, string $localeCode): Pagerfanta;
}
