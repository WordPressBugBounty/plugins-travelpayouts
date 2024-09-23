<?php

namespace Travelpayouts\modules\searchForms\models;

use Travelpayouts;
use Travelpayouts\components\model\ReduxOptionCollectionModel;
use Travelpayouts\components\brands\Subscriptions;
use Travelpayouts\modules\searchForms\components\SearchFormShortcode;
use Travelpayouts\modules\searchForms\models\widgetCode\Direction;
use Travelpayouts\modules\searchForms\models\widgetCode\HotelCity;

/**
 * Class SearchForm
 * @package Travelpayouts\modules\searchForms\models
 * @property-read string|null $type
 * @property-read string|null $url
 * @property-read string|null $widgetId
 * @property-read HotelCity|null $hotelCity
 * @property-read WidgetCode|null $widgetCode
 * @property-read Direction|null $origin
 * @property-read Direction|null $destination
 */
class SearchFormModel extends ReduxOptionCollectionModel
{
    const OLD_FORM_CODE_REGEXP = '/window.TP_FORM_SETTINGS\["(?<widgetId>[\w]+)"\](\s?=\s?)(?<widgetParams>[^;]+)/';
    const NEW_FORM_CODE_REGEXP = '/^<script[^>]*src="[^"]+tp\.media\/content\?[^"]+"[^>]*><\/script>$/';
	const DATE_FORMAT = 'Y-m-d';
	/**
	 * @var string
	 */
	public $title;
	/**
	 * @var array|null
	 */
	public $from_city;
	/**
	 * @var array|null
	 */
	public $to_city;
	/**
	 * @var array|null
	 */
	public $hotel_city;
	/**
	 * @var string
	 */
	public $date_add;
	/**
	 * @var string
	 */
	public $slug;
	/**
	 * @var boolean
	 */
	public $applyParams = false;
	/**
	 * @var string
	 */
	public $code_form;
	/**
	 * @var Direction|null
	 */
	protected $_origin;
	/**
	 * @var Direction|null
	 */
	protected $_destination;
	/**
	 * @var string|null
	 */
	protected $_type;
	/**
	 * @var string|null
	 */
	protected $_url;
	/**
	 * @var string|null
	 */
	protected $_widgetId;
	/**
	 * @var array|null
	 */
	protected $_widgetParams;
	/**
	 * @var HotelCity|null
	 */
	protected $_hotelCity;
	/**
	 * @var WidgetCode|null
	 */
	protected $_widgetCode;

	public function init()
	{
		$this->date_add = date(self::DATE_FORMAT);
	}

	public function rules()
	{
		return array_merge(parent::rules(), [
			[
				[
					'title',
				],
				'string',
				'min' => 3,
			],
			[['title', 'code_form'], 'required'],
			[['code_form'], 'string'],
            /**
             * @see self::codeValidator()
             */
			[['code_form'], 'codeValidator'],
			[['slug'], 'string'],
			[['applyParams'], 'boolean'],
            /**
             * @see self::arrayValidator()
             */
			[['hotel_city', 'to_city', 'from_city'], 'arrayValidator', 'skipOnEmpty' => false],
		]);
	}

	public function attribute_labels()
	{
		return [
			'title' => Travelpayouts::__('Title'),
			'from_city' => Travelpayouts::__('Default departure city'),
			'to_city' => Travelpayouts::__('Default arrival city'),
			'hotel_city' => Travelpayouts::__('Default city or hotel'),
			'date_add' => Travelpayouts::__('Date'),
			'slug' => '',
			'code_form' => Travelpayouts::__('Generated widget code'),
		];
	}

	public function arrayValidator($attribute)
	{
		if ($this->$attribute && !is_array($this->$attribute)) {
			$this->add_error($attribute, Travelpayouts::__('{attribute} has invalid value', [
				'attribute' => $this->get_attribute_label($attribute),
			]));
		}
	}

	/**
	 * Все поисковые формы в видео опций для селекта
	 * @return string[]
	 */
	public function getFormsSelect()
	{
		return $this->selectData($this->findAll());
	}

	/**
	 * Делает из форм опции для селекта
	 * @param self[] $modelList
	 * @return string[]
	 */
	public function selectData($modelList)
	{
		$result = [];
		foreach ($modelList as $model) {
			$result[$model->id] = "{$model->id}. {$model->title}";
		}
		return $result;
	}

	/**
	 * @return self[]
	 */
	public function getFlightsForms()
	{
		return $this->filter('type', [
			SearchFormShortcode::TYPE_AVIA,
			SearchFormShortcode::TYPE_AVIA_HOTEL,
		]);
	}

	/**
	 * @return self[]
	 */
	public function getHotelsForms()
	{
		return $this->filter('type', [
			SearchFormShortcode::TYPE_HOTEL,
			SearchFormShortcode::TYPE_AVIA_HOTEL,
		]);
	}

	/**
	 * @param string $attribute
	 * @param string[] $values
	 * @return self[]
	 */
	private function filter($attribute, $values = [])
	{
		$result = [];
		foreach ($this->findAll() as $model) {
			if ($model->$attribute && in_array($model->$attribute, $values, true)) {
				$result[] = $model;
			}
		}
		return $result;
	}

	protected function optionPath()
	{
		return 'search_forms_search_forms_data';
	}

	public function getOptionValue()
	{
		return $this->redux->getOption($this->optionPath());
	}

	private static function checkActive($forms)
	{
		return !empty($forms);
	}

	public function isActiveFlights()
	{
		return self::checkActive($this->getFlightsForms());
	}

	public function isActiveHotels()
	{
		return self::checkActive($this->getHotelsForms());
	}

	public function getSlugs()
	{
		$slugs = [];
		foreach ($this->data as $form) {
			if (isset($form['slug']) && !empty($form['slug'])) {
				$slugs[] = $form['slug'];
			}
		}

		return $slugs;
	}

	/**
	 * @return string|null
	 */
	public function getType()
	{
        $codeForm = $this->code_form;
        if (!$this->_type && $codeForm) {
            // для старых форм
            if (preg_match('/[\"|\']form_type[\"|\']: [\"|\'](?<type>avia|hotel|avia_hotel)[\"|\'],/', $codeForm, $typeMatches)) {
			$this->_type = $typeMatches['type'];
            } // для новых форм
            elseif (preg_match('/campaign_id=(?<campaignId>\d+)/', $codeForm, $campaignIdMatches)) {
                $this->_type = (int)$campaignIdMatches['campaignId'] === Subscriptions::AVIASALES_ID ? SearchFormShortcode::TYPE_AVIA : SearchFormShortcode::TYPE_HOTEL;
            }
		}
		return $this->_type;
	}

	/**
	 * @return string|null
	 */
	public function getUrl()
	{
		if (!$this->_url && $this->code_form && preg_match('/src="(?<url>.+)"/', $this->code_form, $urlMatches)) {
			$this->_url = $urlMatches['url'];
		}
		return $this->_url;
	}

	/**
	 * @return array|null
	 */
	public function getWidgetParams()
	{
        if (!$this->_widgetParams && $this->code_form && preg_match(self::OLD_FORM_CODE_REGEXP, $this->code_form, $widgetParamMatches)) {
			$this->_widgetParams = json_decode($widgetParamMatches['widgetParams'], true);
		}
		return $this->_widgetParams;
	}

	/**
	 * @return string|null
	 */
	public function getWidgetId()
	{
        if (!$this->_widgetId && $this->code_form && preg_match(self::OLD_FORM_CODE_REGEXP, $this->code_form, $widgetParamMatches)) {
			$this->_widgetId = $widgetParamMatches['widgetId'];
		}
		return $this->_widgetId;
	}

	/**
	 * @return HotelCity|null
	 */
	protected function getHotelCity()
	{
		if (!$this->_hotelCity && $this->hotel_city) {
			$this->_hotelCity = new HotelCity($this->hotel_city);
		}
		return $this->_hotelCity;
	}

	/**
	 * @return WidgetCode|null
	 */
	public function getWidgetCode()
	{
		if (!$this->_widgetCode && $params = $this->getWidgetParams()) {
			$model = new WidgetCode($params);
			if (!$this->applyParams) {
				$model->hotel = $this->hotelCity;
				$model->origin = $this->origin;
				$model->destination = $this->destination;
			}

			$this->_widgetCode = $model;

		}
		return $this->_widgetCode;
	}

	/**
	 * @return Direction|null
	 */
	public function getOrigin()
	{
		if (!$this->_origin && $this->from_city) {
			$this->_origin = new Direction($this->from_city);
		}
		return $this->_origin;
	}

	/**
	 * @return Direction|null
	 */
	public function getDestination()
	{
		if (!$this->_destination && $this->to_city) {
			$this->_destination = new Direction($this->to_city);
		}
		return $this->_destination;
	}

	public function codeValidator($attribute)
	{
        $value = trim($this->$attribute);
        if (!preg_match(self::NEW_FORM_CODE_REGEXP, $value) && !preg_match(self::OLD_FORM_CODE_REGEXP, $value)) {
            $this->add_error($attribute, Travelpayouts::__('{attribute} has invalid value', ['attribute' => $this->get_attribute_label($attribute)]));
        }
	}
}
