<?php

namespace Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class TaxonId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        return $item->getTaxon()->getId();
    }
}
