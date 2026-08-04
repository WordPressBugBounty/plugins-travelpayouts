<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\api;
use Travelpayouts\Vendor\glook\jsonmapper\JsonMapper;
use Travelpayouts\Vendor\glook\jsonmapper\JsonMapperException;

/**
 * The only place where JsonMapper gets configured.
 *
 * Deliberately not a static helper on `ApiResponseObject`: that would force every
 * response class to extend it, and `PricesCheapApiResponse` extends `InjectedModel`.
 */
class ResponseMapper
{
    /**
     * @param array|object|null $data
     * @param string|object $target class name or ready instance
     * @return object
     * @throws JsonMapperException
     */
    public static function map($data, $target)
    {
        $object = is_object($target) ? $target : new $target();

        return self::mapper()->map(self::toObject($data), $object);
    }

    /**
     * @param mixed $items
     * @param string $class
     * @return object[]
     * @throws JsonMapperException
     */
    public static function mapList($items, string $class): array
    {
        if (!is_array($items)) {
            return [];
        }

        // `mapArray()` keeps the original keys, so `array_values()` is required:
        // a response left associative by `afterRequest()` (a price calendar keyed
        // by date) would otherwise turn from a list into a map.
        return array_values(self::mapper()->mapArray(self::toObjectList($items), [], $class));
    }

    private static function mapper(): JsonMapper
    {
        $mapper = new JsonMapper();

        // Fail on a missing field, but only where it is marked `@required`. The
        // markup is product-driven: required means the table row cannot be drawn.
        $mapper->bExceptionOnMissingData = true;

        // An unknown field does not break parsing - the partner adds fields often.
        // Contract tests catch them by diffing against the fixture schema.
        $mapper->bExceptionOnUndefinedProperty = false;

        // `bEnforceMapType` stays at its default (true): it only checks that map()
        // received an object, it has nothing to do with leniency.

        return $mapper;
    }

    /**
     * @param mixed $data
     * @return object
     */
    private static function toObject($data)
    {
        if (is_object($data)) {
            return $data;
        }

        return is_array($data) ? (object)json_decode(json_encode($data), false) : (object)[];
    }

    /**
     * @param array $items
     * @return array
     */
    private static function toObjectList(array $items): array
    {
        return array_map(function ($item) {
            return is_array($item) ? self::toObject($item) : $item;
        }, $items);
    }
}
