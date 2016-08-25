<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Serializer\Handler;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\Handler\SyliusProductSerializerHandler;
use Netgen\Bundle\ContentBrowserBundle\Tests\Backend\Stubs\Product;
use PHPUnit\Framework\TestCase;

class SyliusProductSerializerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\Handler\SyliusProductSerializerHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new SyliusProductSerializerHandler();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\Handler\SyliusProductSerializerHandler::isSelectable
     */
    public function testIsSelectable()
    {
        $this->assertEquals(
            true,
            $this->handler->isSelectable($this->getItem())
        );
    }

    /**
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Product());
    }
}
