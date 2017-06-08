<?php

namespace Netgen\ContentBrowser\Backend\Sylius;

use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    /**
     * Creates a paginator which is used to filter taxons.
     *
     * @param string $parentCode
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createListPaginator($parentCode, $localeCode)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->leftJoin('o.children', 'child')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', $parentCode)
            ->setParameter('locale', $localeCode);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Creates a paginator which is used to search for taxons.
     *
     * @param string $searchText
     * @param string $localeCode
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createSearchPaginator($searchText, $localeCode)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name LIKE :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', '%' . $searchText . '%')
            ->setParameter('locale', $localeCode);

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
