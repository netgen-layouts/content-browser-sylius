<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Product;

use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Location implements LocationInterface, TaxonInterface
{
    public function __construct(private SyliusTaxonInterface $taxon)
    {
    }

    public function getLocationId(): int
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

    public function getTaxon(): SyliusTaxonInterface
    {
        return $this->taxon;
    }
}
