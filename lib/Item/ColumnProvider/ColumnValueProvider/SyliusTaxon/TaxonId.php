<?php

namespace Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Sylius\Taxon\TaxonInterface;

final class TaxonId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        if (!$item instanceof TaxonInterface) {
            return null;
        }

        return $item->getTaxon()->getId();
    }
}
