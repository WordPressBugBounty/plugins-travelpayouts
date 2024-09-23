<?php

namespace Travelpayouts\Vendor\Rollbar;

interface ScrubberInterface
{
    public function scrub(&$data, $replacement);
}
