<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Service;

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class TaxonService extends EntityRepository implements TaxonServiceInterface
{
    public function createListPaginator(string $parentCode, string $localeCode): PagerfantaInterface
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

    public function createSearchPaginator(string $searchText, string $localeCode): PagerfantaInterface
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
