<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Sylius\Product;

use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

final class Location implements LocationInterface, TaxonInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $taxon;

    public function __construct(BaseTaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    public function getLocationId()
    {
        return $this->taxon->getId();
    }

    public function getName()
    {
        return $this->taxon->getName();
    }

    public function getParentId()
    {
        $parentTaxon = $this->taxon->getParent();

        return $parentTaxon instanceof BaseTaxonInterface ?
            $parentTaxon->getId() :
            null;
    }

    public function getTaxon()
    {
        return $this->taxon;
    }
}
