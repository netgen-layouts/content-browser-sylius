<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend\Sylius;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

final class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    public function createListPaginator(string $parentCode, string $localeCode): Pagerfanta
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

    public function createSearchPaginator(string $searchText, string $localeCode): Pagerfanta
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
}
