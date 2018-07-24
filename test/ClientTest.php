<?php
namespace Test\PagaMasTarde\OrdersApiClient;

use PagaMasTarde\OrdersApiClient\Client;
use PagaMasTarde\OrdersApiClient\Model\ApiConfiguration;
use PagaMasTarde\OrdersApiClient\Model\Order;
use Test\PagaMasTarde\OrdersApiClient\AbstractTest;

/**
 * Class ClientTest
 * @package PagaMasTarde\Test
 */
class ClientTest extends AbstractTest
{
    /**
     * Demo Public Key For access the service
     */
    const PUBLIC_KEY = 'tk_fd53cd467ba49022e4f8215e';

    /**
     * Demo Private Key For access the service
     */
    const PRIVATE_KEY = '21e57baa97459f6a';

    /**
     * @var Order
     */
    protected $order;

    /**
     * testClassExists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('PagaMasTarde\OrdersApiClient\Client'));
    }

    /**
     * @return ApiConfiguration
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     */
    public function getApiConfiguration()
    {
        $apiConfiguration = new ApiConfiguration();
        $apiConfiguration
            ->setBaseUri(ApiConfiguration::BASE_URI)
            ->setPrivateKey(self::PRIVATE_KEY)
            ->setPublicKey(self::PUBLIC_KEY)
        ;

        return $apiConfiguration;
    }

    /**
     * testCreateOrder
     *
     * @return bool|false|Order|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     * @throws \ReflectionException
     */
    public function testCreateOrder()
    {

        $orderJson = file_get_contents($this->resourcePath.'Order.json');
        $object = json_decode($orderJson);
        $order = new Order();
        $order->import($object);
        $order->validate();
        $order
            ->setActionUrls(null)
            ->setApiVersion(null)
            ->setConfirmedAt(null)
            ->setCreatedAt(null)
            ->setExpiresAt(null)
            ->setUnconfirmedAt(null)
            ->setGracePeriod(null)
            ->setGracePeriodMonth(null)
            ->setId(null)
            ->setStatus(null)
        ;

        $orderReflectionClass = new \ReflectionClass('PagaMasTarde\OrdersApiClient\Model\Order');
        $property = $orderReflectionClass->getProperty('refunds');
        $property->setAccessible(true);
        $property->setValue($order, null);

        $apiClient = new Client(
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            ApiConfiguration::BASE_URI
        );

        $orderCreated = $apiClient->createOrder($order);
        $this->assertEquals($order->getConfiguration(), $orderCreated->getConfiguration());
        $this->assertEquals($order->getShoppingCart(), $orderCreated->getShoppingCart());
        $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Order', $order);
        $formUrl = $orderCreated->getActionUrls()->getForm();
        $this->assertTrue(Order\Configuration\Urls::urlValidate($formUrl));

        $this->order = $orderCreated;

        return $orderCreated;
    }

    /**
     * testGetOrder
     *
     * @return bool|false|Order|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     * @throws \ReflectionException
     */
    public function testGetOrder()
    {
        if (!$this->order instanceof Order) {
            $this->testCreateOrder();
        }

        $orderJson = file_get_contents($this->resourcePath.'Order.json');
        $object = json_decode($orderJson);
        $order = new Order();
        $order->import($object);

        $apiClient = new Client(
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            ApiConfiguration::BASE_URI
        );

        $orderRetrieved = $apiClient->getOrder($this->order->getId());

        $this->assertEquals($order->getConfiguration(), $orderRetrieved->getConfiguration());
        $this->assertEquals($order->getShoppingCart(), $orderRetrieved->getShoppingCart());
        $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Order', $order);
        $this->order->setConfirmedAt(null);
        $orderRetrieved->setConfirmedAt(null);
        $orderRetrieved->setUnconfirmedAt($this->order->getUnconfirmedAt());
        $this->assertEquals($this->order, $orderRetrieved);

        return $orderRetrieved;
    }

    /**
     * testListOrders
     *
     * @return bool|false|Order[]|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     */
    public function testListOrders()
    {
        $apiClient = new Client(
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            ApiConfiguration::BASE_URI
        );

        $ordersRetrieved = $apiClient->listOrders(array());

        foreach ($ordersRetrieved as $order) {
            $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Order', $order);
        }

        return $ordersRetrieved;
    }

    /**
     * testConfirmOrder
     *
     * @return bool|false|Order|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     * @throws \ReflectionException
     */
    public function testConfirmOrder()
    {
        //Need to mark order as authorized
        return true;

        if (!$this->order instanceof Order) {
            $this->testCreateOrder();
        }

        $orderJson = file_get_contents($this->resourcePath.'Order.json');
        $object = json_decode($orderJson);
        $order = new Order();
        $order->import($object);

        $apiClient = new Client(
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            ApiConfiguration::BASE_URI
        );

        $orderRetrieved = $apiClient->confirmOrder($this->order->getId());

        $this->assertEquals($order->getConfiguration(), $orderRetrieved->getConfiguration());
        $this->assertEquals($order->getShoppingCart(), $orderRetrieved->getShoppingCart());
        $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Order', $order);

        return $orderRetrieved;
    }

    /**
     * testRefundOrder
     *
     * @return bool|false|Order\Refund|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     * @throws \ReflectionException
     */
    public function testRefundOrder()
    {
        //need to mark other as confirmed
        return true;

        if (!$this->order instanceof Order) {
            $this->testCreateOrder();
        }

        $apiClient = new Client(
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            ApiConfiguration::BASE_URI
        );

        $refund = new Order\Refund();
        $refund
            ->setPromotedAmount(0)
            ->setTotalAmount(10)
        ;

        $refund = $apiClient->refundOrder($this->order->getId(), $refund);
        $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Refund', $refund);

        return $refund;
    }
}
