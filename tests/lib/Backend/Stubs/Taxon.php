<?php

namespace Netgen\ContentBrowser\Tests\Backend\Stubs;

use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;

final class Taxon extends BaseTaxon
{
    public function setId($id)
    {
        $this->id = $id;
    }
}
