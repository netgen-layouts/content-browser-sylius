<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product;

interface ProductInterface
{
    /**
     * Returns the Sylius product.
     *
     * @return \Sylius\Component\Product\Model\ProductInterface
     */
    public function getProduct();
}
