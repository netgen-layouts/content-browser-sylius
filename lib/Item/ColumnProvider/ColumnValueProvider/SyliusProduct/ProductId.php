<?php

namespace Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusProduct;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class ProductId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        return $item->getProduct()->getId();
    }
}
