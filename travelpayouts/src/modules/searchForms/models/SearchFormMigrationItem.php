<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models;

use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\httpClient\Client;
use Travelpayouts\components\Model;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\modules\searchForms\models\widgetCode\HotelCity;

/**
 * @property-read string $fromCity
 * @property-read string $toCity
 * @property-read string $hotelCity
 * @property-read string $dateAdd
 */
class SearchFormMigrationItem extends Model
{
    public const CITY_HOTEL_REGEXP = '/(?<id>\d+),?\s(?<type>city|hotel),/';
    public const CITY_REGEXP = '/\[(?<id>\w{3})\]/';

    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    protected $_code_form;
    /**
     * @var string
     */
    protected $_date_add;

    /**
     * @var array|string
     */
    protected $_fromCity;
    /**
     * @var array|string
     */
    protected $_toCity;
    /**
     * @var array|string
     */
    protected $_cityHotel;
    /**
     * @var Client
     */
    protected $_client;
    /**
     * @var string
     */
    protected $_locale = 'ru';
    /**
     * @var string
     */
    protected $_cityHotelType;
    /**
     * Raw legacy value: it carries the whole hotel/city record in braces.
     * @var string
     */
    protected $_cityHotelSource;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;

    }

    /**
     * @param string $value
     * @return self
     */
    public function setFrom_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_REGEXP, $value, $matches)) {
            $this->_fromCity = $matches['id'];
        }
        return $this;

    }

    /**
     * @param string $value
     * @return self
     */
    public function setTo_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_REGEXP, $value, $matches)) {
            $this->_toCity = $matches['id'];
        }
        return $this;
    }

    /**
     * @param $value
     * @return self
     */
    public function setCode_form($value)
    {
        if (is_string($value)) {
            $this->_code_form = str_replace('\\', '', $value);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode_form()
    {
        return $this->_code_form;
    }

    /**
     * @return array|string
     */
    public function getFromCity()
    {
        if (is_string($this->_fromCity)) {
            $data = $this->getCityDataById($this->_fromCity);
            if ($data) {
                $this->_fromCity = $data;
            }
        }

        return $this->_fromCity;
    }

    /**
     * @return array|string
     */
    public function getToCity()
    {
        if (is_string($this->_toCity)) {
            $data = $this->getCityDataById($this->_toCity);
            if ($data) {
                $this->_toCity = $data;
            }
        }

        return $this->_toCity;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setHotel_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_HOTEL_REGEXP, $value, $matches)) {
            $this->_cityHotelSource = $value;
            $this->_cityHotel = $matches['id'];
            if (in_array($matches['type'], ['hotel', 'city'])) {
                $this->_cityHotelType = $matches['type'];
            }
        }
        return $this;
    }

    /**
     * @return array|string
     */
    public function getHotelCity()
    {
        if (is_string($this->_cityHotel) && $this->_cityHotelType) {
            $data = $this->parseHotelCity($this->_cityHotelSource);
            if ($data) {
                $this->_cityHotel = $data;
            }
        }

        return $this->_cityHotel;
    }

    /**
     * The legacy value carries the whole record in braces, so the resolved data is read out of
     * the string instead of fetched: the autocomplete service it came from no longer exists.
     *
     * @param string|null $value
     * @return array|null
     */
    protected function parseHotelCity($value)
    {
        if (!is_string($value) || !preg_match('/\{(?<record>[^}]+)\}/', $value, $matches)) {
            return null;
        }

        $hotelCity = HotelCity::createFromString($matches['record']);

        if (!$hotelCity->search_id) {
            return null;
        }

        return [
            'name' => $hotelCity->name,
            'location' => $hotelCity->location,
            // Accessors cast to int - the endpoint used to return numbers, the regexp yields strings.
            'hotels_count' => $hotelCity->getHotelsCount(),
            'search_id' => $hotelCity->getSearchId(),
            'search_type' => $hotelCity->search_type,
            'country_name' => $hotelCity->country_name,
        ];
    }

    /**
     * @param string $value
     * @return self
     */
    public function setDate_add($value)
    {
        if (is_string($value)) {
            $date = date(SearchFormModel::DATE_FORMAT, $value);
            if ($date) {
                $this->_date_add = $date;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDateAdd()
    {
        return $this->_date_add;
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'from_city' => [$this, 'getFromCity'],
            'to_city' => [$this, 'getToCity'],
            'hotel_city' => [$this, 'getHotelCity'],
            'date_add' => [$this, 'getDateAdd'],
            'code_form',
        ]);
    }

    /**
     * Получаем информацию о городе из эндпоинта
     * @param $id
     * @return mixed|null
     */
    protected function getCityDataById($id)
    {
        $client = $this->getClient();
        $response = $client->get('https://autocomplete.travelpayouts.com/places2', [
            'query' => [
                'locale' => $this->getLocale(),
                'term' => $id,
                'types[]' => 'city',
            ],
        ]);
        if (!$response->isError) {
            $data = $response->getJSON();
            // Guarded: a failed request yields null, and `count(null)` is a TypeError on PHP 8.
            if (is_array($data) && count($data)) {
                return ArrayHelper::getFirst($data);
            }
        }
        return null;
    }

    /**
     * @return SearchFormModel|null
     */
    public function getSearchFormModel()
    {
        $searchForm = (new SearchFormModel($this->toArray()))->setImporting(true);
        try {
            return $searchForm->validate() ? $searchForm->setAllowSaveWithId(true) : null;
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->_client) {
            $this->_client = new CachedClient([
                'timeout' => 15,
                'headers' => [
                    'Accept-Encoding' => 'gzip, deflate',
                    'Accept-Language' => '*',
                ],
            ]);
        }
        return $this->_client;
    }

    /**
     * Seam for tests: `getClient()` builds a `CachedClient` on first use, and resolving a city
     * goes to a live endpoint. Mirrors `ApiModel::setHttpClient()`.
     *
     * @param Client $client
     * @return self
     */
    public function setClient($client): self
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * @param $data
     * @return self[]
     */
    public static function createFromCollection($data)
    {
        $result = [];
        if (is_array($data) && ArrayHelper::isIndexed($data)) {
            self::sortCollectionById($data);
            foreach ($data as $item) {
                $result[] = new self($item);
            }
        }

        return $result;
    }

    protected static function sortCollectionById(&$data)
    {
        usort($data, static function ($a, $b) {
            $idA = isset($a['id']) ? $a['id'] : null;
            $idB = isset($b['id']) ? $b['id'] : null;
            if ($idA == $idB) {
                return 0;
            }
            return ($idA < $idB) ? -1 : 1;
        });
    }

}
