<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class SyliusProductConfiguratorHandler implements ConfiguratorHandlerInterface
{
    /**
     * Returns if the item is selectable based on provided config.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param array $config
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item, array $config)
    {
        return true;
    }
}
