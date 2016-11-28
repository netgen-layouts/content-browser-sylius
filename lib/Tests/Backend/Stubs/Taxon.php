<?php

namespace Netgen\ContentBrowser\Tests\Backend\Stubs;

use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;

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
