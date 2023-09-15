<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    public function __construct(private int $id, private ?int $parentId = null) {}

    public function getLocationId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return 'This is a name';
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
