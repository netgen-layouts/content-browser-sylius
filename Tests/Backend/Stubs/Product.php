<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs;

use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct
{
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
