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
    protected $taxon;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $taxon
     */
    public function __construct(BaseTaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getLocationId()
    {
        return $this->taxon->getId();
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->taxon->getId();
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->taxon->getName();
    }

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        $parentTaxon = $this->taxon->getParent();

        return $parentTaxon instanceof BaseTaxonInterface ?
            $parentTaxon->getId() :
            null;
    }

    /**
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return true;
    }

    /**
     * Returns the Sylius taxon.
     *
     * @return \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    public function getTaxon()
    {
        return $this->taxon;
    }
}
