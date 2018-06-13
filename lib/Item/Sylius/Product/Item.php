<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as SyliusProductInterface;

final class Item implements ItemInterface, ProductInterface
{
    /**
     * @var \Sylius\Component\Product\Model\ProductInterface
     */
    private $product;

    public function __construct(SyliusProductInterface $product)
    {
        $this->product = $product;
    }

    public function getValue()
    {
        return $this->product->getId();
    }

    public function getName(): string
    {
        return (string) $this->product->getName();
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getProduct(): SyliusProductInterface
    {
        return $this->product;
    }
}
