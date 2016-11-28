<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend\Sylius;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter products by taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, $localeCode)
    {
        $root = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->createQueryBuilderWithLocaleCode($localeCode);
        $queryBuilder
            ->innerJoin('o.taxons', 'taxon')
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

    /**
     * Creates a paginator which is used to search for products.
     *
     * @param string $searchText
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createSearchPaginator($searchText, $localeCode)
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
     *
     * @param string $localeCode
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilderWithLocaleCode($localeCode)
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