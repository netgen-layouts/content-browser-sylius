<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Taxon;

interface TaxonInterface
{
    /**
     * Returns the Sylius taxon.
     *
     * @return \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    public function getTaxon();
}
