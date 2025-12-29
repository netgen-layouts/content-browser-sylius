<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Service;

use Pagerfanta\PagerfantaInterface;

interface TaxonServiceInterface
{
    /**
     * Creates a paginator which is used to filter taxons.
     *
     * @return \Pagerfanta\PagerfantaInterface<\Sylius\Component\Taxonomy\Model\TaxonInterface>
     */
    public function createListPaginator(string $parentCode, string $localeCode): PagerfantaInterface;

    /**
     * Creates a paginator which is used to search for taxons.
     *
     * @return \Pagerfanta\PagerfantaInterface<\Sylius\Component\Taxonomy\Model\TaxonInterface>
     */
    public function createSearchPaginator(string $searchText, string $localeCode): PagerfantaInterface;
}
