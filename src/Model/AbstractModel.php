<?php

namespace PagaMasTarde\OrdersApiClient\Model;

use Exceptions\Data\IntegrityException;
use Nayjest\StrCaseConverter\Str;

/**
 * Class AbstractModel
 *
 * @package PagaMasTarde\OrdersApiClient\Model
 */
abstract class AbstractModel implements ModelInterface
{
    /**
     * Export as Array the object recursively
     *
     * @return array
     */
    public function export()
    {
        $result = array();
        foreach ($this as $key => $value) {
            if ($value) {
                $result[Str::toSnakeCase($key)] = $this->parseValue($value);
            }
        }

        return $result;
    }

    /**
     * Parse the value of the object depending of type.
     *
     * @param $value
     *
     * @return array
     */
    public function parseValue($value)
    {
        if (is_array($value) && !empty($value)) {
            $valueArray = array();
            foreach ($value as $subKey => $subValue) {
                if (is_object($subValue) && $subValue instanceof AbstractModel) {
                    $valueArray[Str::toSnakeCase($subKey)] = $subValue->export();
                } else {
                    $valueArray[Str::toSnakeCase($subKey)] = $subValue;
                }
            }
            return $valueArray;
        }
        if (is_object($value) && $value instanceof AbstractModel && !empty($value)) {
            return $value->export();
        }

        return $value;
    }

    /**
     * Fill Up the Order from the json_decode(false) result of the API response.
     *
     * @param $object
     */
    public function import($object)
    {
        if (is_object($object)) {
            $properties = get_object_vars($object);
            foreach ($properties as $key => $value) {
                if (property_exists($this, lcfirst(Str::toCamelCase($key)))) {
                    if (is_object($value)) {
                        $objectProperty = $this->{lcfirst(Str::toCamelCase($key))};

                        if ($objectProperty instanceof AbstractModel) {
                            $objectProperty->import($value);
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
