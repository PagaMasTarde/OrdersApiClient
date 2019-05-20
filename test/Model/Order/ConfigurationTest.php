<?php

namespace Test\Pagantis\OrdersApiClient\Model\Order;

use Pagantis\OrdersApiClient\Model\Order\Configuration;
use Test\Pagantis\OrdersApiClient\AbstractTest;

/**
 * Class ConfigurationTest
 * @package Test\Pagantis\OrdersApiClient\Model\Order
 */
class ConfigurationTest extends AbstractTest
{
    /**
     * Test Constructor creates entities
     */
    public function testConstruct()
    {
        $configuration = new Configuration();
        $this->assertInstanceOf(
            'Pagantis\OrdersApiClient\Model\Order\Configuration\Channel',
            $configuration->getChannel()
        );
        $this->assertInstanceOf(
            'Pagantis\OrdersApiClient\Model\Order\Configuration\Urls',
            $configuration->getUrls()
        );
    }

    /**
     * Test setter and getter for purchase country
     */
    public function testSetPurchaseCountry()
    {
        $configuration = new Configuration();
        $purchaseCountry = 'IT';
        $configuration->setPurchaseCountry($purchaseCountry);
        $this->assertSame($purchaseCountry, $configuration->getPurchaseCountry());
    }
}
