<?php

namespace Travelpayouts\Vendor\Rollbar\Payload;

interface ContentInterface extends \Serializable
{
    public function getKey();
}
