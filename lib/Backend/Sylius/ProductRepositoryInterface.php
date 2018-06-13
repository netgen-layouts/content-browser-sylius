<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend\Sylius;

use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter products by taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, $localeCode);

    /**
     * Creates a paginator which is used to search for products.
     *
     * @param string $searchText
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createSearchPaginator($searchText, $localeCode);
}
