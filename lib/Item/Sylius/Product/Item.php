<?php

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;

final class Item implements ItemInterface, ProductInterface
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    private $product;

    public function __construct(BaseProductInterface $product)
    {
        $this->product = $product;
    }

    public function getValue()
    {
        return $this->product->getId();
    }

    public function getName()
    {
        return $this->product->getName();
    }

    public function isVisible()
    {
        return true;
    }

    public function isSelectable()
    {
        return true;
    }

    public function getProduct()
    {
        return $this->product;
    }
}
