<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Repository;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

interface TaxonRepositoryInterface extends BaseTaxonRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter taxons.
     *
     * @return \Pagerfanta\Pagerfanta<\Sylius\Component\Taxonomy\Model\TaxonInterface>
     */
    public function createListPaginator(string $parentCode, string $localeCode): Pagerfanta;

    /**
     * Creates a paginator which is used to search for taxons.
     *
     * @return \Pagerfanta\Pagerfanta<\Sylius\Component\Taxonomy\Model\TaxonInterface>
     */
    public function createSearchPaginator(string $searchText, string $localeCode): Pagerfanta;
}
