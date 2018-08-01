<?php

namespace Test\PagaMasTarde\OrdersApiClient\Method;

use PagaMasTarde\OrdersApiClient\Exception\ValidationException;
use Faker\Factory;
use Httpful\Http;
use Httpful\Request;
use PagaMasTarde\OrdersApiClient\Method\GetOrderMethod;
use PagaMasTarde\OrdersApiClient\Model\ApiConfiguration;
use Test\PagaMasTarde\OrdersApiClient\AbstractTest;

/**
 * Class GetOrderMethodTest
 *
 * @package Test\PagaMasTarde\OrdersApiClient\Method;
 */
class GetOrderMethodTest extends AbstractTest
{
    /**
     * testEndpointConstant
     */
    public function testEndpointConstant()
    {
        $constant = GetOrderMethod::ENDPOINT;
        $this->assertEquals('/orders', $constant);
    }

    /**
     * testSetOrderId
     *
     * @throws \ReflectionException
     */
    public function testSetOrderId()
    {
        $faker = Factory::create();
        $orderId = $faker->uuid;
        $apiConfigurationMock = $this->getMock('PagaMasTarde\OrdersApiClient\Model\ApiConfiguration');
        $getOrderMethod = new GetOrderMethod($apiConfigurationMock);
        $getOrderMethod->setOrderId($orderId);
        $reflectGetOrderMethod = new \ReflectionClass('PagaMasTarde\OrdersApiClient\Method\GetOrderMethod');
        $property = $reflectGetOrderMethod->getProperty('orderId');
        $property->setAccessible(true);
        $this->assertEquals($orderId, $property->getValue($getOrderMethod));
    }

    /**
     * testGetOrder
     *
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function testGetOrder()
    {
        $orderJson = file_get_contents($this->resourcePath.'Order.json');
        $responseMock = $this->getMockBuilder('Httpful\Response')->disableOriginalConstructor()->getMock();
        $responseMockReflect = new \ReflectionClass('Httpful\Response');
        $property = $responseMockReflect->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($responseMock, json_decode($orderJson));

        $apiConfigurationMock = $this->getMock('PagaMasTarde\OrdersApiClient\Model\ApiConfiguration');
        $getOrderMethod = new GetOrderMethod($apiConfigurationMock);
        $this->assertFalse($getOrderMethod->getOrder());
        $reflectGetOrderMethod = new \ReflectionClass('PagaMasTarde\OrdersApiClient\Method\GetOrderMethod');
        $property = $reflectGetOrderMethod->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($getOrderMethod, $responseMock);

        $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Model\Order', $getOrderMethod->getOrder());
    }

    /**
     * testPrepareRequest
     *
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function testPrepareRequest()
    {
        $faker = Factory::create();
        $url = $faker->url;
        $orderId = $faker->uuid;
        $apiConfiguration = new ApiConfiguration();
        $apiConfiguration->setBaseUri($url);
        $getOrderMethod = new GetOrderMethod($apiConfiguration);
        $reflectGetOrderMethod = new \ReflectionClass('PagaMasTarde\OrdersApiClient\Method\GetOrderMethod');
        $method = $reflectGetOrderMethod->getMethod('prepareRequest');
        $method->setAccessible(true);
        $property = $reflectGetOrderMethod->getProperty('request');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($getOrderMethod));
        $getOrderMethod->setOrderId($orderId);
        $method->invoke($getOrderMethod);
        /** @var Request $request */
        $request = $property->getValue($getOrderMethod);
        $this->assertInstanceOf('Httpful\Request', $request);
        $this->assertSame(Http::GET, $request->method);
        $uri = $url . GetOrderMethod::ENDPOINT . GetOrderMethod::SLASH . $orderId;
        $this->assertSame($uri, $request->uri);
    }

    /**
     * testCall
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     */
    public function testCall()
    {
        $apiConfigurationMock = $this->getMock('PagaMasTarde\OrdersApiClient\Model\ApiConfiguration');
        $getOrderMethod = new GetOrderMethod($apiConfigurationMock);
        try {
            $getOrderMethod->call();
            $this->assertTrue(false);
        } catch (ValidationException $exception) {
            $this->assertInstanceOf('PagaMasTarde\OrdersApiClient\Exception\ValidationException', $exception);
        }
    }
}
