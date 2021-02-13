<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Taxon;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Item implements ItemInterface, LocationInterface, TaxonInterface
{
    private SyliusTaxonInterface $taxon;

    public function __construct(SyliusTaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    public function getLocationId(): int
    {
        return $this->taxon->getId();
    }

    public function getValue(): int
    {
        return $this->taxon->getId();
    }

    public function getName(): string
    {
        return (string) $this->taxon->getName();
    }

    public function getParentId(): ?int
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
