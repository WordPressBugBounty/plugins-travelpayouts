<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use Travelpayouts\admin\redux\base\ExtensionField;
use Travelpayouts\components\HtmlHelper;

if (!class_exists('TravelpayoutsSettingsFramework_Travelpayouts_Autocomplete')) {
    class TravelpayoutsSettingsFramework_Travelpayouts_Autocomplete extends ExtensionField
    {
        const WIDGET_ID = 'TravelpayoutsAutocomplete';
        protected $_assets = ['reduxWidgets'];

        public $url;
        public $optionLabel;
        public $inputName;
        public $value;
        public $noOptionsMessage;
        public $loadingMessage;
        public $placeholder;
        public $sourcePath;

        public function init()
        {
            $this->noOptionsMessage = Travelpayouts::__('Not found');
            $this->loadingMessage = Travelpayouts::__('Loading results');
            $this->placeholder = Travelpayouts::__('Select...');
            $this->inputName = isset($this->field['name'])
                ? $this->field['name']
                : null;
        }

        public function rules()
        {
            return [
                [
                    [
                        'url',
                        'optionLabel',
                        'inputName',
                        'value',
                    ],
                    'required',
                ],
                [
                    [
                        'url',
                        'optionLabel',
                        'noOptionsMessage',
                        'loadingMessage',
                        'placeholder',
                        'sourcePath',
                    ],
                    'string',
                    'skipOnEmpty' => true
                ]
            ];
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         * @since 1.0.0
         */
        public function render()
        {
            $attrs = isset($this->field['attributes']) ? $this->field['attributes'] : [];
            $this->attributes = $attrs;
            if ($this->validate()) {
                echo HtmlHelper::tag('div', [
                    'class' => 'regular-text tp-text',
                ], HtmlHelper::reactWidget(self::WIDGET_ID, $this->attributes));
            } else {
                var_dump($this->getErrors());
            }
        }
    }
}
