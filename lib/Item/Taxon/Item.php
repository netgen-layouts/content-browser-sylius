<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Taxon;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Item implements ItemInterface, LocationInterface, TaxonInterface
{
    public function __construct(private SyliusTaxonInterface $taxon)
    {
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
        return $this->taxon->getParent()?->getId();
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
