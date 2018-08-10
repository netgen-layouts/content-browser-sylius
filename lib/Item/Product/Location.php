<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Item\Product;

use Netgen\ContentBrowser\Item\LocationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as SyliusTaxonInterface;

final class Location implements LocationInterface, TaxonInterface
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

    public function getTaxon(): SyliusTaxonInterface
    {
        return $this->taxon;
    }
}
