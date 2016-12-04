<?php

namespace Netgen\ContentBrowser\Tests\Item\Serializer\Handler;

use Netgen\ContentBrowser\Item\Sylius\Product\Item;
use Netgen\ContentBrowser\Item\Serializer\Handler\SyliusProductSerializerHandler;
use Netgen\ContentBrowser\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class SyliusProductSerializerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\Handler\SyliusProductSerializerHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new SyliusProductSerializerHandler();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\Handler\SyliusProductSerializerHandler::isSelectable
     */
    public function testIsSelectable()
    {
        $this->assertTrue(
            $this->handler->isSelectable($this->getItem())
        );
    }

    /**
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Product());
    }
}
