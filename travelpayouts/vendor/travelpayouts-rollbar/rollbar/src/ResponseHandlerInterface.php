<?php

namespace Travelpayouts\Vendor\Rollbar;

interface ResponseHandlerInterface
{
    public function handleResponse($payload, $response);
}
