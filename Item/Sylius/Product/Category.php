<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

class Category implements CategoryInterface, TaxonInterface
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
     * Returns the ID.
     *
     * @return int|string
     */
    public function getId()
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
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return 'sylius_product';
    }

    /**
     * Returns the category parent ID.
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
     * Returns the Sylius taxon.
     *
     * @return \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    public function getTaxon()
    {
        return $this->taxon;
    }
}
