<?php

namespace Netgen\ContentBrowser\Item\Sylius\Taxon;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

class Item implements ItemInterface, LocationInterface, TaxonInterface
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

    public function getValue()
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

    public function isVisible()
    {
        return true;
    }

    public function isSelectable()
    {
        return true;
    }

    public function getTaxon()
    {
        return $this->taxon;
    }
}
