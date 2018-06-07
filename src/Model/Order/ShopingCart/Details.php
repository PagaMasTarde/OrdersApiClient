<?php

namespace PagaMasTarde\OrdersApiClient\Model\Order\ShopingCart;

use Exceptions\Data\IntegrityException;
use Exceptions\Data\ValidationException;
use Nayjest\StrCaseConverter\Str;
use PagaMasTarde\OrdersApiClient\Model\AbstractModel;
use PagaMasTarde\OrdersApiClient\Model\Order\ShopingCart\Details\Product;

/**
 * Class Details
 * @package PagaMasTarde\OrdersApiClient\Model\Order\ShopingCart
 */
class Details extends AbstractModel
{
    /**
     * @var Product[]
     */
    protected $products;

    /**
     * @var int $shipping_cost Shipping cost for the order
     */
    protected $shippingCost;

    /**
     * Details constructor.
     */
    public function __construct()
    {
        $this->products = array();
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     *
     * @return Details
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * @return int
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @param int $shippingCost
     *
     * @return Details
     */
    public function setShippingCost($shippingCost)
    {
        if ($shippingCost >= 0 && is_int($shippingCost)) {
            $this->shippingCost = $shippingCost;
            return $this;
        }

        throw new ValidationException('Shipping cost has to be natural number');
    }

    /**
     * Overwrite import to fill products correctly
     *
     * @param $object
     */
    public function import($object)
    {
        if (is_object($object)) {
            $properties = get_object_vars($object);
            foreach ($properties as $key => $value) {
                if (property_exists($this, lcfirst(Str::toCamelCase($key)))) {
                    if (is_array($value)) {
                        foreach ($value as $product) {
                            $addProduct = new Product();
                            $addProduct->import($product);
                            $this->addProduct($addProduct);
                        }
                    } else {
                        $this->{lcfirst(Str::toCamelCase($key))} = $value;
                    }
                } else {
                    throw new IntegrityException('Property ' . lcfirst(Str::toCamelCase($key)) . ' Not found');
                }
            }
        }
    }
}
