<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    private int $id;

    private ?int $parentId;

    public function __construct(int $id, ?int $parentId = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

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
