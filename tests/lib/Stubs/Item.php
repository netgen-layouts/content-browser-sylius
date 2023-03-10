<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;

final class Item implements ItemInterface
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return 'This is a name (' . $this->value . ')';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }
}
