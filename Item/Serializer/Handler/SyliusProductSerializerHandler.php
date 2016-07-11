<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer\Handler;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerHandlerInterface;

class SyliusProductSerializerHandler implements ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item)
    {
        return true;
    }
}
