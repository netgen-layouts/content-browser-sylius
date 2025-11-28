<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Product;

use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Location implements LocationInterface, TaxonInterface
{
    public int $locationId {
        get => $this->taxon->getId();
    }

    public string $name {
        get => $this->taxon->getName() ?? '';
    }

    public ?int $parentId {
        get => $this->taxon->getParent()?->getId();
    }

    public function __construct(
        public private(set) SyliusTaxonInterface $taxon,
    ) {}
}
