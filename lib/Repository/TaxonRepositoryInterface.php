<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Repository;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

/**
 * @extends \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface<\Sylius\Component\Taxonomy\Model\TaxonInterface>
 */
interface TaxonRepositoryInterface extends BaseTaxonRepositoryInterface
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
