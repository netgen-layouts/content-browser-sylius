<?php

namespace Netgen\ContentBrowser\Item\Serializer\Handler;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerHandlerInterface;

class SyliusProductSerializerHandler implements ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item)
    {
        return true;
    }
}
