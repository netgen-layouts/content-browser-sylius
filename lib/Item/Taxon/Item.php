<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Taxon;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Item implements ItemInterface, LocationInterface, TaxonInterface
{
    public int $locationId {
        get => $this->taxon->getId();
    }

    public int $value {
        get => $this->taxon->getId();
    }

    public string $name {
        get => $this->taxon->getName() ?? '';
    }

    public ?int $parentId {
        get => $this->taxon->getParent()?->getId();
    }

    public true $isVisible {
        get => true;
    }

    public true $isSelectable {
        get => true;
    }

    public function __construct(
        private(set) SyliusTaxonInterface $taxon,
    ) {}
}
