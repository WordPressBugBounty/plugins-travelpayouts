<?php

namespace Travelpayouts\Vendor\Rollbar\Truncation;

use \Travelpayouts\Vendor\Rollbar\Payload\EncodedPayload;

class RawStrategy extends AbstractStrategy
{
    public function execute(EncodedPayload $payload)
    {
        return $payload;
    }
}
