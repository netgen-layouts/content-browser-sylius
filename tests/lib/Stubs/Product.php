<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Sylius\Component\Product\Model\Product as BaseProduct;

final class Product extends BaseProduct
{
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
