<?php

namespace Travelpayouts\modules\widgets\components;

use Travelpayouts\components\brands\PlatformsEndpoint;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\shortcodes\ShortcodeModel;

/**
 * Class WidgetShortcode
 * @package Travelpayouts\modules\widgets\components
 */
class WidgetShortcode extends ShortcodeModel
{
    public const TRAVELPAYOUTS_SHORTCODE_REGEX = '/^<script[^>]* src="(?<url>[^"]+\/content\?([^"]+)).*?<\/script>$/';

    /**
     * Matched on the stored content: entity-only text from a filtered author must not decode into a script.
     */
    private const SCRIPT_URL_REGEX = '/^<script[^>]*\ssrc="(?<url>[^"]+\/content\?[^"]+)"/';

    /**
     * @var string|null
     */
    public $content;

    /**
     * @var null|string
     */
    protected $_scriptUrl = null;

    public static function shortcodeTags()
    {
        return ['tp_widget'];
    }

    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new self();
        if (is_string($content)) {
            $model->content = $content;
        }
        return $model->render();
    }

    public function render()
    {
        $scriptUrl = $this->getScriptUrl();
        if (!$scriptUrl) {
            return '';
        }

        return HtmlHelper::scriptFile(esc_url_raw($this->moveToWidgetDomain($scriptUrl)), [
            'async' => 'async',
            'charset' => 'utf-8',
        ]);
    }

    protected function moveToWidgetDomain(string $scriptUrl): string
    {
        $scriptHost = $this->getScriptHost();
        $platformResponse = PlatformsEndpoint::getInstance()->getData();
        $widgetDomain = $platformResponse ? $platformResponse->widget_domain : null;

        return $scriptHost && $widgetDomain ? str_replace($scriptHost, $widgetDomain, $scriptUrl) : $scriptUrl;
    }

    /**
     * Проверяем, является ли переданный контент виджетом Travelpayouts
     * @param $data
     * @return bool
     */
    public static function isTravelpayoutsWidget($data): bool
    {
        return is_string($data) && preg_match(self::TRAVELPAYOUTS_SHORTCODE_REGEX, trim($data));
    }

    protected function getScriptUrl(): ?string
    {
        if (!$this->_scriptUrl && $this->content && preg_match(self::SCRIPT_URL_REGEX, trim($this->content), $matches)) {
            $url = html_entity_decode($matches['url']);
            if (in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
                $this->_scriptUrl = $url;
            }
        }
        return $this->_scriptUrl;
    }

    /**
     * Получаем параметры виджета из URL скрипта
     * @return array
     */
    public function getWidgetParameters(): array
    {
        if ($scriptUrl = $this->getScriptUrl()) {
            $parsedUrl = parse_url($scriptUrl);
            if (isset($parsedUrl['query'])) {
                $queryParams = [];
                parse_str($parsedUrl['query'], $queryParams);
                return $queryParams;
            }
        }
        return [];
    }

    /**
     * Получаем домен скрипта виджета
     * @return string
     */
    public function getScriptHost(): ?string
    {
        if ($scriptUrl = $this->getScriptUrl()) {
            $host = parse_url($scriptUrl, PHP_URL_HOST);
            return $host ?: null;
        }
        return null;
    }
}
