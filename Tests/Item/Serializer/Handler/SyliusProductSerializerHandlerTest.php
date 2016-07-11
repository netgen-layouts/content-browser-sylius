<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Serializer\Handler;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\Handler\SyliusProductSerializerHandler;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Product;

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
        self::assertEquals(
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
