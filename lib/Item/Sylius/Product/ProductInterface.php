<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Sylius\Component\Product\Model\ProductInterface as SyliusProductInterface;

interface ProductInterface
{
    /**
     * Returns the Sylius product.
     */
    public function getProduct(): SyliusProductInterface;
}
