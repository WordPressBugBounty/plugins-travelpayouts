<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use Travelpayouts\admin\redux\base\ExtensionField;
use Travelpayouts\components\HtmlHelper;

if (!class_exists('TravelpayoutsSettingsFramework_osc_accordion')) {
    class TravelpayoutsSettingsFramework_osc_accordion extends ExtensionField
    {
        public $extension_dir;
        public $extension_url;
        protected $_assets = ['accordion'];

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         * @since TravelpayoutsSettingsFramework 1.0.0
         */
        public function render()
        {
            // If options is NOT empty, the process

            if ($this->field['position'] === 'start') {
                $isOpened = isset($this->field['open']) && $this->field['open'];
                $headerClassList = [
                    'travelpayouts-accordion__header',
                ];
                if ($isOpened) {
                    $headerClassList[] = 'travelpayouts-accordion__header--visible';
                }

                $startContent = [
                    '</td></tr></table>',
                    HtmlHelper::openTag('div', [
                        'class' => 'travelpayouts-accordion',
                    ]),
                    HtmlHelper::openTag('div', [
                        'class' => implode(' ', $headerClassList),
                        'tabindex' => '0',
                    ]),
                    HtmlHelper::tag('div', [
                        'class' => 'travelpayouts-accordion__title',
                    ], $this->field['title']),
                    isset($this->field['subtitle']) && !empty($this->field['subtitle'])
                        ? HtmlHelper::tag('div', [
                        'class' => 'travelpayouts-accordion__subtitle',
                    ], $this->field['subtitle'])
                        : null,
                    HtmlHelper::closeTag('div'),
                    HtmlHelper::openTag('div', [
                        'class' => 'travelpayouts-accordion__content',
                        'style' => !$isOpened
                            ? 'display:none;'
                            : '',
                    ]),
                    '<table class="form-table tp-admin-section no-border" style="margin-top: 0;"><tbody class="tp-admin-table-body"><tr class="tp-admin-table-body-row" style="border-bottom:0; display:none;">',
                    '<th class="tp-admin-table-body-head" style="padding-top:0;"></th><td class="tp-admin-table-body-cell" style="padding-top:0;">',
                ];
                echo implode('', $startContent);

            } elseif ($this->field['position'] === 'end') {
                $endContent = [
                    '</td></tr></table></div>',
                    '</div>',
                    '<table class="form-table tp-admin-section no-border" style="margin-top: 0;"><tbody class="tp-admin-table-body"><tr class="tp-admin-table-body-row" style="border-bottom:0; display:none;">',
                    '<th style="padding-top:0;"></th><td style="padding-top:0;">',
                ];
                echo implode('', $endContent);
            }
        } //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         * @since TravelpayoutsSettingsFramework 1.0.0
         */
        public function enqueue()
        {

        }
    }
}
