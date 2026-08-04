<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\api;

use ArrayAccess;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\interfaces\Arrayable;
use Travelpayouts\traits\ArrayableTrait;
use Travelpayouts\traits\SingletonTrait;

abstract class ApiResponseObject extends BaseInjectedObject implements Arrayable, ArrayAccess
{
    use SingletonTrait;
    use ArrayableTrait;

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        return $this->$offset;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }

    /**
     * @param array $response
     * @return static
     */
    public static function createFromArray(array $response)
    {
        // Static method, no error bag here, and callers already expect nullable,
        // so a parse failure returns null instead of escaping to a public page.
        try {
            /** @var static $mappedResponse */
            $mappedResponse = ResponseMapper::map($response, static::class);
        } catch (\Throwable $e) {
            return null;
        }

        return $mappedResponse;
    }
}
