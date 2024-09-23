<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TravelpayoutsSettingsFramework_Travelpayouts_Clear_Tables_Cache')) {

    class TravelpayoutsSettingsFramework_Travelpayouts_Clear_Tables_Cache
    {
        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @return      void
         * @since       1.0.0
         * @access      public
         */
        public function __construct($field = [], $value = '', $parent = '')
        {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @return      void
         * @since       1.0.0
         * @access      public
         */
        public function render()
        {
            $html = '<div>';
            $html .= '<span id="travelpayouts_travelpayouts_clear_tables_cache_button" class="tp-button tp-button--primary" style="margin: 0 0 0 5px;">' . Travelpayouts::__('Delete cache') . '</span>';
            $html .= '</div>';
            echo $html;
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @throws Exception
         */
        public function enqueue()
        {
            wp_enqueue_script(
                'redux-field-travelpayouts-clear-tables-cache-js',
                Travelpayouts::getAlias('@webadmin/redux/extensions/travelpayouts_clear_tables_cache/field_travelpayouts_clear_tables_cache.min.js'),
                ['jquery'],
                time(),
                true
            );
        }
    }
}
