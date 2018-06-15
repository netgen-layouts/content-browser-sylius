<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Backend\Stubs;

use Sylius\Component\Product\Model\Product as BaseProduct;

final class Product extends BaseProduct
{
    public function setId($id): void
    {
        $this->id = $id;
    }
}
