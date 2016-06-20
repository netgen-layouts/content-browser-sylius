<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Category;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Value;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

class SyliusProductBackend implements BackendInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * @var \Sylius\Component\Product\Repository\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface $taxonRepository
     * @param \Sylius\Component\Product\Repository\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getDefaultSections()
    {
        return $this->buildCategories(
            $this->taxonRepository->findRootNodes()
        );
    }

    /**
     * Loads a  category by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If category does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface
     */
    public function loadCategory($id)
    {
        /** @var \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon */
        $taxon = $this->taxonRepository->find($id);

        return $this->buildCategory($taxon);
    }

    /**
     * Loads the item by its value ID.
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

        return $this->buildItem($product);
    }

    /**
     * Returns the categories below provided category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getSubCategories(CategoryInterface $category)
    {
        $taxons = $this->taxonRepository->findChildren($category->getTaxon());

        return $this->buildCategories($taxons);
    }

    /**
     * Returns the count of categories below provided category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubCategoriesCount(CategoryInterface $category)
    {
        return count($this->getSubCategories($category));
    }

    /**
     * Returns the category items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(CategoryInterface $category, $offset = 0, $limit = 25)
    {
        /** @var \Pagerfanta\Pagerfanta $products */
        $products = $this->productRepository->createByTaxonPaginator(
            $category->getTaxon()
        );

        $products->setMaxPerPage($limit);
        $products->setCurrentPage((int)($offset / $limit) + 1);

        return $this->buildItems(
            iterator_to_array(
                $products->getCurrentPageResults()
            ),
            $category->getTaxon()
        );
    }

    /**
     * Returns the category items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubItemsCount(CategoryInterface $category)
    {
        /** @var \Pagerfanta\Pagerfanta $products */
        $products = $this->productRepository->createByTaxonPaginator(
            $category->getTaxon()
        );

        return $products->getNbResults();
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
        /** @var \Pagerfanta\Pagerfanta $products */
        $products = $this->productRepository->createFilterPaginator(
            array(
                'name' => $searchText,
            )
        );

        $products->setMaxPerPage($limit);
        $products->setCurrentPage((int)($offset / $limit) + 1);

        return $this->buildItems(
            iterator_to_array(
                $products->getCurrentPageResults()
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
        /** @var \Pagerfanta\Pagerfanta $products */
        $products = $this->productRepository->createFilterPaginator(
            array(
                'name' => $searchText,
            )
        );

        return $products->getNbResults();
    }

    /**
     * Builds the category from provided taxon.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface
     */
    protected function buildCategory(TaxonInterface $taxon)
    {
        return new Category($taxon);
    }

    /**
     * Builds the categories from provided taxons.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface[] $taxons
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    protected function buildCategories(array $taxons)
    {
        return array_map(
            function (TaxonInterface $taxon) {
                return $this->buildCategory($taxon);
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
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected function buildItem(ProductInterface $product, TaxonInterface $parentTaxon = null)
    {
        return new Item(
            new Value(
                $product
            ),
            $parentTaxon
        );
    }

    /**
     * Builds the items from provided products.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface[] $products
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parentTaxon
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
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
