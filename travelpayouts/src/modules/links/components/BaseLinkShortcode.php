<?php

namespace Travelpayouts\modules\links\components;

use DateTime;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts;
use Travelpayouts\components\ErrorHelper;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\settings\Settings;

/**
 * Class LinkModel
 */
abstract class BaseLinkShortcode extends ShortcodeModel
{
    const LINK_MARKER = 'wpplugin_link';

    /**
     * @var string
     */
    public $new_tab = false;
    /**
     * @var string
     */
    public $text_link;
    /**
     * @var string
     */
    protected $subid;

    /**
     * @Inject
     * @var Settings
     */
    protected $settingsModule;

    /**
     * @Inject
     * @var Account
     */
    protected $accountModule;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text_link'], 'required'],
            [['shortcode_name', 'new_tab'], 'safe'],
            [['subid'], 'string'],
        ]);
    }

    /**
     * Добавляет количесто дней из параметра $days к текущей дате
     * @param int $days
     * @param string $format
     * @return string
     */
    protected function date_time_add_days($days = 1, $format = 'Y-m-d')
    {
        try {
            $date_time = new DateTime();
            $date_time->modify('+' . $days . ' Day');

            return $date_time->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Формирование ссылки из параметра url и настроек
     * @param $url
     * @return string
     */
    protected function get_link_html($url)
    {
        $newTab = true === filter_var(
                $this->new_tab,
                FILTER_VALIDATE_BOOLEAN
            );

        $button_attributes = [
            'href' => UrlHelper::getInstance()->getUrl($url),
        ];
        $settingsModuleData = $this->settingsModule->data;
        if ($settingsModuleData->get('nofollow')) {
            $button_attributes['rel'] = 'nofollow';
        }
        if ($settingsModuleData->get('target_url') || $newTab) {
            $button_attributes['target'] = '_blank';
        }

        $button_attributes['class'] = TRAVELPAYOUTS_TEXT_DOMAIN . '-link';

        return Html::tag(
            'a',
            $button_attributes,
            $this->text_link
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->get_link_html($this->get_url());
    }

    abstract protected function get_url();

    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new static();
        $model->attributes = $attributes;
        if (!$model->validate()) {
            return ErrorHelper::render_errors($tag, $model->getErrors());
        }
        return $model->render();
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'new_tab' => Travelpayouts::__('Open link in a new tab'),
            'subid' => Travelpayouts::__('Sub ID'),
        ]);
    }

    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(), [
            'text_link' => $this->fieldInput(),
            'new_tab' => $this->fieldCheckbox()->setDefault(true),
            'subid' => $this->fieldInput(),
        ]);
    }

}
