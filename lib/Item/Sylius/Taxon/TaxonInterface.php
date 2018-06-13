<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Taxon;

use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

interface TaxonInterface
{
    /**
     * Returns the Sylius taxon.
     */
    public function getTaxon(): SyliusTaxonInterface;
}
