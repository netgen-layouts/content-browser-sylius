<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Product;

interface ProductInterface
{
    /**
     * Returns the Sylius product.
     *
     * @return \Sylius\Component\Product\Model\ProductInterface
     */
    public function getProduct();
}
