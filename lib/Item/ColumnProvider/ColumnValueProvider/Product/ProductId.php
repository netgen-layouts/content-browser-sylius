<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Product;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Sylius\Item\Product\ProductInterface;

final class ProductId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item): ?string
    {
        if (!$item instanceof ProductInterface) {
            return null;
        }

        return (string) $item->getProduct()->getId();
    }
}
