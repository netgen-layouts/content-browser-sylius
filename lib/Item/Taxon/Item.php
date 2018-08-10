<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Taxon;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Item implements ItemInterface, LocationInterface, TaxonInterface
{
    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    private $taxon;

    public function __construct(SyliusTaxonInterface $taxon)
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

    public function getName(): string
    {
        return (string) $this->taxon->getName();
    }

    public function getParentId()
    {
        $parentTaxon = $this->taxon->getParent();

        return $parentTaxon instanceof SyliusTaxonInterface ?
            $parentTaxon->getId() :
            null;
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getTaxon(): SyliusTaxonInterface
    {
        return $this->taxon;
    }
}
