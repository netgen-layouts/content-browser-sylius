<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class Item implements ItemInterface, ProductInterface
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    protected $product;

    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    protected $parentTaxon;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface $product
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parentTaxon
     */
    public function __construct(BaseProductInterface $product, TaxonInterface $parentTaxon = null)
    {
        $this->product = $product;
        $this->parentTaxon = $parentTaxon;
    }

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return 'sylius_product';
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->product->getId();
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->product->getName();
    }

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentTaxon instanceof TaxonInterface ?
            $this->parentTaxon->getId() :
            null;
    }

    /**
     * Returns the Sylius product.
     *
     * @return \Sylius\Component\Product\Model\ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }
}
