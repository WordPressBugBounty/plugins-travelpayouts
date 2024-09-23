<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
use Travelpayouts\admin\redux\base\Extension;

// Don't duplicate me!
if( !class_exists( 'TravelpayoutsSettingsFramework_extension_osc_accordion' ) ) {
    class TravelpayoutsSettingsFramework_extension_osc_accordion extends Extension{
    }
}
