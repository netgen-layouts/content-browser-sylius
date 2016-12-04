<?php

namespace Netgen\ContentBrowser\Item\Renderer\TemplateValueProvider;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\TemplateValueProviderInterface;

class SyliusProductTemplateValueProvider implements TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return array
     */
    public function getValues(ItemInterface $item)
    {
        return array(
            'product' => $item->getProduct(),
        );
    }
}
