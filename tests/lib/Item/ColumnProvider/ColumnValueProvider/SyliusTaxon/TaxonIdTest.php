<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30200) {
            self::markTestSkipped('Sylius tests require Symfony 3.2 or later to run.');
        }

        $this->provider = new TaxonId();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId::getValue
     */
    public function testGetValue(): void
    {
        $taxon = new Taxon();
        $taxon->setId(42);

        $item = new Item($taxon);

        self::assertSame(
            '42',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\SyliusTaxon\TaxonId::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
