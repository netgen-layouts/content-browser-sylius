<?php

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;

class Item implements ItemInterface, ProductInterface
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Product\Model\ProductInterface $product
     */
    public function __construct(BaseProductInterface $product)
    {
        $this->product = $product;
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
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
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
