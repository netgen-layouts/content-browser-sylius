<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Repository;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter products by taxon.
     *
     * @return \Pagerfanta\PagerfantaInterface<\Sylius\Component\Product\Model\ProductInterface>
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, string $localeCode): PagerfantaInterface;

    /**
     * Creates a paginator which is used to search for products.
     *
     * @return \Pagerfanta\PagerfantaInterface<\Sylius\Component\Product\Model\ProductInterface>
     */
    public function createSearchPaginator(string $searchText, string $localeCode): PagerfantaInterface;
}
