<?php

namespace Travelpayouts\Vendor\Rollbar;

interface DataBuilderInterface
{
    public function makeData($level, $toLog, $context);
}
