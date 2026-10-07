<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components\elementor;

/**
 * Older Elementor saves control values without kses for users lacking unfiltered_html.
 */
final class WidgetContent
{
    public const WIDGET_NAME = TRAVELPAYOUTS_PLUGIN_NAME . '_shortcode_widget';

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function sanitizeOnSave(array $data): array
    {
        if (!current_user_can('unfiltered_html') && isset($data['elements']) && is_array($data['elements'])) {
            $data['elements'] = self::sanitizeElements($data['elements']);
        }

        return $data;
    }

    /**
     * @param array<int, mixed> $elements
     * @return array<int, mixed>
     */
    private static function sanitizeElements(array $elements): array
    {
        return array_map(static function ($element) {
            if (!is_array($element)) {
                return $element;
            }

            if (($element['widgetType'] ?? null) === self::WIDGET_NAME && is_string($element['settings']['content'] ?? null)) {
                $element['settings']['content'] = wp_kses_post($element['settings']['content']);
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                $element['elements'] = self::sanitizeElements($element['elements']);
            }

            return $element;
        }, $elements);
    }
}
