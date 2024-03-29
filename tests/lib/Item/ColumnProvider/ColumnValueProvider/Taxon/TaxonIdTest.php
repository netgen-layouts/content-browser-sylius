<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Sylius\Tests\Item\ColumnProvider\ColumnValueProvider\Taxon;

use Netgen\ContentBrowser\Sylius\Item\ColumnProvider\ColumnValueProvider\Taxon\TaxonId;
use Netgen\ContentBrowser\Sylius\Item\Taxon\Item;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Item as StubItem;
use Netgen\ContentBrowser\Sylius\Tests\Stubs\Taxon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TaxonId::class)]
final class TaxonIdTest extends TestCase
{
    private TaxonId $provider;

    protected function setUp(): void
    {
        $this->provider = new TaxonId();
    }

    public function testGetValue(): void
    {
        $taxon = new Taxon();
        $taxon->setId(42);

        $item = new Item($taxon);

        self::assertSame('42', $this->provider->getValue($item));
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem('value')));
    }
}
