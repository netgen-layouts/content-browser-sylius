<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Taxon;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Sylius\Item\Taxon\TaxonInterface;

final class TaxonId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item): ?string
    {
        if (!$item instanceof TaxonInterface) {
            return null;
        }

        return (string) $item->getTaxon()->getId();
    }
}
