<?php

namespace PagaMasTarde\OrdersApiClient\Method;

use Httpful\Http;
use Httpful\Request;
use Httpful\Response;
use PagaMasTarde\OrdersApiClient\Exception\ValidationException;
use PagaMasTarde\OrdersApiClient\Model\Order;

/**
 * Class GetOrderMethod
 *
 * @package PagaMasTarde\OrdersApiClient\Method
 */
class GetOrderMethod extends AbstractMethod
{
    /**
     * Get Order Endpoint
     */
    const ENDPOINT = '/orders';

    /**
     * @var string $orderId
     */
    protected $orderId;

    /**
     * @param string $orderId
     *
     * @return GetOrderMethod
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return $this|AbstractMethod
     * @throws ValidationException
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \PagaMasTarde\OrdersApiClient\Exception\HttpException
     */
    public function call()
    {
        if (is_string($this->orderId)) {
            $this->prepareRequest();
            return $this->setResponse($this->request->send());
        }
        throw new ValidationException('Please set OrderId');
    }

    /**
     * @return bool|Order
     * @throws ValidationException
     */
    public function getOrder()
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $order = new Order();
            $order->import($this->getResponse()->body);
            return $order;
        }

        return false;
    }

    /**
     * prepareRequest
     */
    protected function prepareRequest()
    {
        if (!$this->request instanceof Request) {
            $this->request = $this->getRequest()
                ->method(Http::GET)
                ->uri(
                    $this->apiConfiguration->getBaseUri()
                    . self::ENDPOINT
                    . self::SLASH
                    . $this->orderId
                )
            ;
        }
    }
}
