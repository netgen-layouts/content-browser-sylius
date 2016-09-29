<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class SyliusProductBackend implements BackendInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * @var \Sylius\Component\Core\Repository\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Sylius\Component\Locale\Context\LocaleContextInterface
     */
    protected $localeContext;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface $taxonRepository
     * @param \Sylius\Component\Core\Repository\ProductRepositoryInterface $productRepository
     * @param \Sylius\Component\Locale\Context\LocaleContextInterface $localeContext
     */
    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductRepositoryInterface $productRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
        $this->localeContext = $localeContext;
    }

    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getDefaultSections()
    {
        return $this->buildLocations(
            $this->taxonRepository->findRootNodes()
        );
    }

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface
     */
    public function loadLocation($id)
    {
        /** @var \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon */
        $taxon = $this->taxonRepository->find($id);

        if (!$taxon instanceof TaxonInterface) {
            throw new NotFoundException(
                sprintf(
                    'Location with "%s" ID not found.',
                    $id
                )
            );
        }

        return $this->buildLocation($taxon);
    }

    /**
     * Loads the item by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($id)
    {
        /** @var \Sylius\Component\Product\Model\ProductInterface $product */
        $product = $this->productRepository->find($id);

        if (!$product instanceof ProductInterface) {
            throw new NotFoundException(
                sprintf(
                    'Item with "%s" ID not found.',
                    $id
                )
            );
        }

        return $this->buildItem($product);
    }

    /**
     * Returns the locations below provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location)
    {
        $taxons = $this->taxonRepository->findBy(
            array(
                'parent' => $location->getTaxon(),
            )
        );

        return $this->buildLocations($taxons);
    }

    /**
     * Returns the count of locations below provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location)
    {
        return count($this->getSubLocations($location));
    }

    /**
     * Returns the location items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        $taxon = $location->getTaxon();
        $rootTaxon = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->productRepository->createListQueryBuilder();

        $queryBuilder
            ->innerJoin('o.taxons', 'taxon')
                ->andWhere($queryBuilder->expr()->eq('taxon.root', ':root'))
                ->andWhere($queryBuilder->expr()->orX(
                    'taxon = :taxon',
                    ':left < taxon.left AND taxon.right < :right'
                ))
            ->setParameter('root', $rootTaxon)
            ->setParameter('taxon', $taxon)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int)($offset / $limit) + 1);

        return $this->buildItems(
            iterator_to_array(
                $paginator->getCurrentPageResults()
            ),
            $taxon
        );
    }

    /**
     * Returns the location items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location)
    {
        $taxon = $location->getTaxon();
        $rootTaxon = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->productRepository->createListQueryBuilder();

        $queryBuilder
            ->innerJoin('o.taxons', 'taxon')
                ->andWhere($queryBuilder->expr()->eq('taxon.root', ':root'))
                ->andWhere($queryBuilder->expr()->orX(
                    'taxon = :taxon',
                    ':left < taxon.left AND taxon.right < :right'
                ))
            ->setParameter('root', $rootTaxon)
            ->setParameter('taxon', $taxon)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));

        return $paginator->getNbResults();
    }

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function search($searchText, $offset = 0, $limit = 25)
    {
        $queryBuilder = $this->productRepository->createListQueryBuilder();

        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->like('translation.name', ':name'),
                    $queryBuilder->expr()->eq('translation.locale', ':locale')
                )
            )
            ->setParameter('name', '%' . $searchText . '%')
            ->setParameter('locale', $this->localeContext->getLocaleCode());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage((int)($offset / $limit) + 1);

        return $this->buildItems(
            iterator_to_array(
                $paginator->getCurrentPageResults()
            )
        );
    }

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     *
     * @return int
     */
    public function searchCount($searchText)
    {
        $queryBuilder = $this->productRepository->createListQueryBuilder();

        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->like('translation.name', ':name'),
                    $queryBuilder->expr()->eq('translation.locale', ':locale')
                )
            )
            ->setParameter('name', '%' . $searchText . '%')
            ->setParameter('locale', $this->localeContext->getLocaleCode());

        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));

        return $paginator->getNbResults();
    }

    /**
     * Builds the location from provided taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location
     */
    protected function buildLocation(TaxonInterface $taxon)
    {
        return new Location($taxon);
    }

    /**
     * Builds the locations from provided taxons.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Location[]
     */
    protected function buildLocations(array $taxons)
    {
        return array_map(
            function (TaxonInterface $taxon) {
                return $this->buildLocation($taxon);
            },
            $taxons
        );
    }

    /**
     * Builds the item from provided product.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface $product
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parentTaxon
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item
     */
    protected function buildItem(ProductInterface $product, TaxonInterface $parentTaxon = null)
    {
        return new Item($product, $parentTaxon);
    }

    /**
     * Builds the items from provided products.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface[] $products
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parentTaxon
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item[]
     */
    protected function buildItems(array $products, TaxonInterface $parentTaxon = null)
    {
        return array_map(
            function (ProductInterface $product) use ($parentTaxon) {
                return $this->buildItem($product, $parentTaxon);
            },
            $products
        );
    }
}
