<?php

namespace Travelpayouts\Vendor\Rollbar;
use Travelpayouts\Vendor\Rollbar\Payload\Level;
use Travelpayouts\Vendor\Rollbar\Payload\Payload;

interface TransformerInterface
{
    /**
     * @param Payload $payload
     * @param Level $level
     * @param \Exception | \Throwable $toLog
     * @param $context
     * @return Payload
     */
    public function transform(Payload $payload, $level, $toLog, $context);
}
