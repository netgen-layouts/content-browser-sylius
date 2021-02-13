<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string|null
     */
    private $parentId;

    /**
     * @param int|string $id
     * @param int|string|null $parentId
     */
    public function __construct($id, $parentId = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    public function getLocationId(): int
    {
        return (int) $this->id;
    }

    public function getName(): string
    {
        return 'This is a name';
    }

    public function getParentId(): ?int
    {
        return $this->parentId !== null ? (int) $this->parentId : null;
    }
}
