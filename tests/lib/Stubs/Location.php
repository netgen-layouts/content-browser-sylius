<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    public string $name {
        get => 'This is a name';
    }

    public function __construct(
        public private(set) int $locationId,
        public private(set) ?int $parentId = null,
    ) {}
}
