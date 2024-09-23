<?php

namespace Travelpayouts\Vendor\Rollbar\Senders;
use Travelpayouts\Vendor\Rollbar\Payload\Payload;
use Travelpayouts\Vendor\Rollbar\Payload\EncodedPayload;

interface SenderInterface
{
    public function send(EncodedPayload $payload, $accessToken);
    public function sendBatch($batch, $accessToken);
    public function wait($accessToken, $max);
    public function toString();
}
