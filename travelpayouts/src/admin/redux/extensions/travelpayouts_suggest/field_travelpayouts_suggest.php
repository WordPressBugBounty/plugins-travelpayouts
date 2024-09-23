<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TravelpayoutsSettingsFramework_Travelpayouts_Suggest')) {
    class TravelpayoutsSettingsFramework_Travelpayouts_Suggest
    {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since TravelpayoutsSettingsFramework 1.0.0
         */
        public function __construct($field = [], $value = '', $parent)
        {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;
            $this->value_raw = '';
            $this->value_label = '';

            if(isset($this->value['raw'])) {
                $this->value_raw = $this->value['raw'];
            }

            if(isset($this->value['label'])) {
                $this->value_label = $this->value['label'];
            }
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since TravelpayoutsSettingsFramework 1.0.0
         */
        public function render()
        {
            if (!empty($this->field['data']) && empty($this->field['options'])) {
                if (empty($this->field['args'])) {
                    $this->field['args'] = [];
                }

                $this->field['options'] = $this->parent->get_wordpress_data($this->field['data'], $this->field['args']);
                $this->field['class'] .= ' hasOptions ';
            }

            if (empty($this->value) && !empty($this->field['data']) && !empty($this->field['options'])) {
                $this->value = $this->field['options'];
            }

            //if (isset($this->field['text_hint']) && is_array($this->field['text_hint'])) {
            $qtip_title = isset($this->field['text_hint']['title']) ? 'qtip-title="' . $this->field['text_hint']['title'] . '" ' : '';
            $qtip_text = isset($this->field['text_hint']['content']) ? 'qtip-content="' . $this->field['text_hint']['content'] . '" ' : '';
            //}

            $readonly = (isset($this->field['readonly']) && $this->field['readonly']) ? ' readonly="readonly"' : '';
            $autocomplete = (isset($this->field['autocomplete']) && $this->field['autocomplete'] == false) ? ' autocomplete="off"' : '';

            $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';
            echo '<input ' . $qtip_title . $qtip_text . 'type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[label]" ' . $placeholder . 'value="' . esc_attr($this->value_label) . '" class="regular-text tp-input travelpayouts-suggest ' . $this->field['class'] . '"' . $readonly . $autocomplete . ' />';
            echo '<input type="hidden" class="select-raw" name="' . $this->field['name'] . '[raw]' . '" value="' . esc_attr($this->value_raw) . '" />';
        }

        public function enqueue()
        {
            wp_enqueue_script(
                'jquery-ui',
                plugin_dir_url(__FILE__) . 'jquery-ui.min.js',
                ['jquery', 'jquery-ui-core', 'redux-js'],
                time(),
                true
            );
            wp_enqueue_script(
                'redux-field-travelpayouts_suggest',
                plugin_dir_url(__FILE__) . 'travelpayouts_suggest.js',
                ['jquery', 'jquery-ui-core', 'redux-js'],
                time(),
                true
            );
        }
    }
}
