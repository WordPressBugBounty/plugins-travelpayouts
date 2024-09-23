<?php

namespace Travelpayouts\modules\widgets\components;

use Travelpayouts\components\shortcodes\ShortcodeModel;

/**
 * Class WidgetShortcode
 * @package Travelpayouts\modules\widgets\components
 */
class WidgetShortcode extends ShortcodeModel
{
    protected $content;

    public static function shortcodeTags()
    {
        return ['tp_widget'];
    }

    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new self();
        $model->content = $content;

        return $model->render();
    }

    public function render()
    {
        return $this->content;
    }
}