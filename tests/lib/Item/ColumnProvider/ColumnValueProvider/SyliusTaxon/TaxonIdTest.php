<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId;
use Netgen\ContentBrowser\Item\Sylius\Taxon\Item;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Taxon;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class TaxonIdTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId
     */
    private $provider;

    public function setUp()
    {
        if (Kernel::VERSION_ID < 30200) {
            $this->markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

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

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId::getValue
     */
    public function testGetValueWithInvalidItem()
    {
        $this->assertNull($this->provider->getValue(new StubItem()));
    }
}
