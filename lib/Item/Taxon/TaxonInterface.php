<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Taxon;

use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

interface TaxonInterface
{
    /**
     * Returns the Sylius taxon.
     */
    public function getTaxon(): SyliusTaxonInterface;
}
