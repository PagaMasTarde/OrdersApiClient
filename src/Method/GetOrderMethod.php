<?php

namespace PagaMasTarde\OrdersApiClient\Method;

use Exceptions\Data\ValidationException;
use Exceptions\Http\Server\ServerErrorException;
use Httpful\Http;
use Httpful\Response;
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
    const ENDPOINT = 'api/v1/orders';

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
     * @return $this
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     *
     * @throws ServerErrorException
     */
    public function call()
    {
        if (is_string($this->orderId)) {
            $response = $this->getRequest()
                ->method(Http::GET)
                ->uri(
                    $this->apiConfiguration->getBaseUri() .
                    self::SLASH .
                    self::ENDPOINT .
                    self::SLASH .
                    $this->orderId
                )
                ->send();

            if (!$response->hasErrors()) {
                $this->response = $response;

                return $this;
            }

            return $this->parseHttpException($response->code);
        }
        throw new ValidationException('Please set OrderId');
    }

    /**
     * @return Order | false
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
}
