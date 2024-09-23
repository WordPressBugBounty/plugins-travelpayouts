<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Don't duplicate me!
if (!class_exists('TravelpayoutsSettingsFramework_extension_travelpayouts_settings_import')) {

    /**
     * Main TravelpayoutsSettingsFramework custom_field extension class
     * @since       3.1.6
     */
    class TravelpayoutsSettingsFramework_extension_travelpayouts_settings_import extends TravelpayoutsSettingsFramework
    {
        public $field_name;
        // Protected vars
        protected $parent;
        public $extension_url;
        public $extension_dir;
        public static $theInstance;

        /**
         * Class Constructor. Defines the args for the extions class
         * @param array $sections Panel sections.
         * @param array $args Class constructor arguments.
         * @param array $extra_tabs Extra panel tabs.
         * @return      void
         * @since       1.0.0
         * @access      public
         */
        public function __construct($reduxFramework)
        {
            $this->parent = $reduxFramework;
            if (empty($this->extension_dir)) {
                $this->extension_dir = trailingslashit(str_replace('\\', '/', __DIR__));
            }
            $this->field_name = 'travelpayouts_settings_import';
            self::$theInstance = $this;
            add_filter('redux_travelpayouts/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, [&$this, 'overload_field_path']); // Adds the local field
        }

        public function getInstance()
        {
            return self::$theInstance;
        }

        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path()
        {
            return __DIR__ . '/field_' . $this->field_name . '.php';
        }
    }
}
