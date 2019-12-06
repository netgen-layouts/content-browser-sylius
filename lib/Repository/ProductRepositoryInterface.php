<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Repository;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter products by taxon.
     *
     * @return \Pagerfanta\Pagerfanta<\Sylius\Component\Product\Model\ProductInterface>
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, string $localeCode): Pagerfanta;

    /**
     * Creates a paginator which is used to search for products.
     *
     * @return \Pagerfanta\Pagerfanta<\Sylius\Component\Product\Model\ProductInterface>
     */
    public function createSearchPaginator(string $searchText, string $localeCode): Pagerfanta;
}
