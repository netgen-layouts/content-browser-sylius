<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId;
use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use PHPUnit\Framework\TestCase;

class TaxonIdTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new TaxonId();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId::getValue
     */
    public function testGetValue()
    {
        $taxon = new Taxon();
        $taxon->setId(42);

        $item = new Item($taxon);

        $this->assertEquals(
            42,
            $this->provider->getValue($item)
        );
    }
}
