<?php

namespace Travelpayouts\Vendor\Rollbar;

interface FilterInterface
{
    public function shouldSend($payload, $accessToken);
}
