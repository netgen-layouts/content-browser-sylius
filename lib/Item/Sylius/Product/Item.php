<?php

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

class Item implements ItemInterface, ProductInterface
{
    const TYPE = 'sylius_product';

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
    public function __construct(BaseProductInterface $product, BaseTaxonInterface $parentTaxon = null)
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
        return static::TYPE;
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
        return $this->parentTaxon instanceof BaseTaxonInterface ?
            $this->parentTaxon->getId() :
            null;
    }

    /**
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
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
