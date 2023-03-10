<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as SyliusProductInterface;

final class Item implements ItemInterface, ProductInterface
{
    public function __construct(private SyliusProductInterface $product)
    {
    }

    public function getValue(): int
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
