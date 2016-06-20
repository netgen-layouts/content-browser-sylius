<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class Item implements ItemInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface
     */
    protected $value;

    /**
     * @var \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    protected $parentTaxon;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Value $value
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parentTaxon
     */
    public function __construct(Value $value, TaxonInterface $parentTaxon = null)
    {
        $this->value = $value;
        $this->parentTaxon = $parentTaxon;
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
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->value->getName();
    }

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentTaxon instanceof TaxonInterface ?
            $this->parentTaxon->getId() :
            null;
    }

    /**
     * Returns the value.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }
}
