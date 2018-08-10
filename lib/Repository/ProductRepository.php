<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Repository;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    public function createByTaxonPaginator(TaxonInterface $taxon, string $localeCode): Pagerfanta
    {
        $root = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->createQueryBuilderWithLocaleCode($localeCode);
        $queryBuilder
            ->innerJoin('o.productTaxons', 'product_taxon')
            ->innerJoin('product_taxon.taxon', 'taxon')
            ->andWhere($queryBuilder->expr()->eq('taxon.root', ':root'))
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('taxon', ':taxon'),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->lt(':left', 'taxon.left'),
                        $queryBuilder->expr()->lt('taxon.right', ':right')
                    )
                )
            )
            ->setParameter('root', $root)
            ->setParameter('taxon', $taxon)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        return $this->getPaginator($queryBuilder);
    }

    public function createSearchPaginator(string $searchText, string $localeCode): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilderWithLocaleCode($localeCode);
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->like('translation.name', ':name')
            )
            ->setParameter('name', '%' . $searchText . '%')
        ;

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Creates a query builder to filter products by locale.
     */
    private function createQueryBuilderWithLocaleCode(string $localeCode): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;

        return $queryBuilder;
    }
}
