<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;

class Value implements ValueInterface, ProductInterface
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
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->product->getId();
    }

    /**
     * Returns the value name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->product->getName();
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
