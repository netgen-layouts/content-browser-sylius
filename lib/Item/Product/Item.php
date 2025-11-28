<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Product;

use Netgen\ContentBrowser\Item\ItemInterface;
use Sylius\Component\Product\Model\ProductInterface as SyliusProductInterface;

final class Item implements ItemInterface, ProductInterface
{
    public int $value {
        get => $this->product->getId();
    }

    public string $name {
        get => $this->product->getName() ?? '';
    }

    public true $isVisible {
        get => true;
    }

    public true $isSelectable {
        get => true;
    }

    public function __construct(
        public private(set) SyliusProductInterface $product,
    ) {}
}
