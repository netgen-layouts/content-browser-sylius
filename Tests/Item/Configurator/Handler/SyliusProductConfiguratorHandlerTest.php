<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Configurator\Handler;

use Netgen\Bundle\ContentBrowserBundle\Item\Sylius\Product\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\SyliusProductConfiguratorHandler;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Product;

class SyliusProductConfiguratorHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\SyliusProductConfiguratorHandler
     */
    protected $configurator;

    public function setUp()
    {
        $this->configurator = new SyliusProductConfiguratorHandler();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\SyliusProductConfiguratorHandler::isSelectable
     */
    public function testIsSelectable()
    {
        self::assertEquals(
            true,
            $this->configurator->isSelectable($this->getItem(), array())
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
