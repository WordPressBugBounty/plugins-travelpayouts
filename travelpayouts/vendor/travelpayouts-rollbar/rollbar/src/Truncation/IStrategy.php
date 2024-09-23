<?php

namespace Travelpayouts\Vendor\Rollbar\Truncation;

use \Travelpayouts\Vendor\Rollbar\Payload\EncodedPayload;

interface IStrategy
{
    /**
     * @param array $payload
     * @return array
     */
    public function execute(EncodedPayload $payload);
    
    /**
     * @param array $payload
     * @return array
     */
    public function applies(EncodedPayload $payload);
}
