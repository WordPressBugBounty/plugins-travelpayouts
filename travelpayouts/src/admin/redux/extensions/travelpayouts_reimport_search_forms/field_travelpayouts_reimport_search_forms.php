<?php

use Travelpayouts\components\HtmlHelper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TravelpayoutsSettingsFramework_Travelpayouts_Reimport_Search_Forms')) {

    class TravelpayoutsSettingsFramework_Travelpayouts_Reimport_Search_Forms
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
            echo HtmlHelper::tag('span', ['class' => 'tp-button tp-button--secondary travelpayouts-migrate-search-forms'], Travelpayouts::__('Re-import search forms'));
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
                'redux-field-travelpayouts-reimport-search-forms-js',
                Travelpayouts::getAlias('@webadmin/redux/extensions/travelpayouts_reimport_search_forms/field_travelpayouts_reimport_search_forms.min.js'),
                ['jquery'],
                time(),
                true
            );
        }
    }
}
