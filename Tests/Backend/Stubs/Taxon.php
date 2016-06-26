<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs;

use Sylius\Component\Core\Model\Taxon as BaseTaxon;

class Taxon extends BaseTaxon
{
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
