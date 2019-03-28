<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Stubs;

use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;

final class Taxon extends BaseTaxon
{
    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
