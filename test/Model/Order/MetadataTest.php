<?php

namespace Test\PagaMasTarde\OrdersApiClient\Model\Order;

use Faker\Factory;
use PagaMasTarde\OrdersApiClient\Model\Order\Metadata;
use PHPUnit\Framework\TestCase;

/**
 * Class MetadataTest
 * @package Test\PagaMasTarde\OrdersApiClient\Model\Order
 */
class MetadataTest extends TestCase
{
    /**
     * testAddMetadata
     *
     * @expectedException \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     */
    public function testAddMetadata()
    {
        $faker = Factory::create();
        $metadata = new Metadata();
        $key = $faker->randomLetter;
        $value = $faker->sentence;
        $metadata->addMetadata($key, $value);
        $metadataExport = $metadata->export();
        $this->assertSame($value, $metadataExport[$key]);

        $metadata->addMetadata(array(), array());
    }

    /**
     * testImport
     *
     * @throws \PagaMasTarde\OrdersApiClient\Exception\ValidationException
     */
    public function testImport()
    {
        $metadata = new Metadata();

        $orderJson = file_get_contents('test/Resources/Order.json');
        $object = json_decode($orderJson);
        $object = $object->metadata;

        $metadata->import($object);
        $this->assertEquals($object, json_decode(json_encode($metadata->export())));
    }
}
