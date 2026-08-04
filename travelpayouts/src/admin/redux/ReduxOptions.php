<?php

namespace Travelpayouts\admin\redux;

use Travelpayouts;
use Travelpayouts\components\LanguageHelper;

class ReduxOptions
{
    public const FLIGHTS_SOURCE_AVIASALES_RU = 0;
    public const FLIGHTS_SOURCE_JETRADAR_COM = 1;
    public const FLIGHTS_SOURCE_AVIASALES_KZ = 2;
    public const FLIGHTS_SOURCE_JETRADAR_COM_BR = 3;
    public const FLIGHTS_SOURCE_CA_JETRADAR_COM = 4;
    public const FLIGHTS_SOURCE_JETRADAR_CH = 5;
    public const FLIGHTS_SOURCE_JETRADAR_AT = 6;
    public const FLIGHTS_SOURCE_JETRADAR_BE = 7;
    public const FLIGHTS_SOURCE_JETRADAR_CO_NL = 8;
    public const FLIGHTS_SOURCE_JETRADAR_GR = 9;
    public const FLIGHTS_SOURCE_JETRADAR_COM_AU = 10;
    public const FLIGHTS_SOURCE_JETRADAR_DE = 11;
    public const FLIGHTS_SOURCE_JETRADAR_ES = 12;
    public const FLIGHTS_SOURCE_JETRADAR_FR = 13;
    public const FLIGHTS_SOURCE_JETRADAR_IT = 14;
    public const FLIGHTS_SOURCE_JETRADAR_PT_ = 15;
    public const FLIGHTS_SOURCE_IE_JETRADAR_COM_ = 16;
    public const FLIGHTS_SOURCE_JETRADAR_CO_UK = 17;
    public const FLIGHTS_SOURCE_JETRADAR_HK_ = 18;
    public const FLIGHTS_SOURCE_JETRADAR_IN_ = 19;
    public const FLIGHTS_SOURCE_JETRADAR_CO_NZ = 20;
    public const FLIGHTS_SOURCE_JETRADAR_PH = 21;
    public const FLIGHTS_SOURCE_JETRADAR_PL_ = 22;
    public const FLIGHTS_SOURCE_JETRADAR_SG_ = 23;
    public const FLIGHTS_SOURCE_JETRADAR_CO_TH = 24;
    public const FLIGHTS_SOURCE_AVIASALES_BY = 26;
    public const FLIGHTS_SOURCE_AVIASALES_KG = 27;
    public const FLIGHTS_SOURCE_AVIASALES_UA = 28;
    public const FLIGHTS_SOURCE_AVIASALES_COM = 29;
    public const FLIGHTS_SOURCE_AVIASALES_UZ = 30;

    public const CURRENCY_RUB = 'RUB';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_AED = 'AED';
    public const CURRENCY_AMD = 'AMD';
    public const CURRENCY_ARS = 'ARS';
    public const CURRENCY_AUD = 'AUD';
    public const CURRENCY_AZN = 'AZN';
    public const CURRENCY_BDT = 'BDT';
    public const CURRENCY_BGN = 'BGN';
    public const CURRENCY_BHD = 'BHD';
    public const CURRENCY_BRL = 'BRL';
    public const CURRENCY_BYN = 'BYN';
    public const CURRENCY_CAD = 'CAD';
    public const CURRENCY_CHF = 'CHF';
    public const CURRENCY_CLP = 'CLP';
    public const CURRENCY_CNY = 'CNY';
    public const CURRENCY_DKK = 'DKK';
    public const CURRENCY_CZK = 'CZK';
    public const CURRENCY_COP = 'COP';
    public const CURRENCY_EGP = 'EGP';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_GEL = 'GEL';
    public const CURRENCY_HKD = 'HKD';
    public const CURRENCY_HUF = 'HUF';
    public const CURRENCY_IDR = 'IDR';
    public const CURRENCY_ILS = 'ILS';
    public const CURRENCY_INR = 'INR';
    public const CURRENCY_IQD = 'IQD';
    public const CURRENCY_ISK = 'ISK';
    public const CURRENCY_JOD = 'JOD';
    public const CURRENCY_JPY = 'JPY';
    public const CURRENCY_KGS = 'KGS';
    public const CURRENCY_KRW = 'KRW';
    public const CURRENCY_KWD = 'KWD';
    public const CURRENCY_KZT = 'KZT';
    public const CURRENCY_LKR = 'LKR';
    public const CURRENCY_LYD = 'LYD';
    public const CURRENCY_MUR = 'MUR';
    public const CURRENCY_MXN = 'MXN';
    public const CURRENCY_MYR = 'MYR';
    public const CURRENCY_NGN = 'NGN';
    public const CURRENCY_NOK = 'NOK';
    public const CURRENCY_NPR = 'NPR';
    public const CURRENCY_NZD = 'NZD';
    public const CURRENCY_OMR = 'OMR';
    public const CURRENCY_PEN = 'PEN';
    public const CURRENCY_PHP = 'PHP';
    public const CURRENCY_PKR = 'PKR';
    public const CURRENCY_PLN = 'PLN';
    public const CURRENCY_QAR = 'QAR';
    public const CURRENCY_RON = 'RON';
    public const CURRENCY_RSD = 'RSD';
    public const CURRENCY_SAR = 'SAR';
    public const CURRENCY_SEK = 'SEK';
    public const CURRENCY_SGD = 'SGD';
    public const CURRENCY_THB = 'THB';
    public const CURRENCY_TJS = 'TJS';
    public const CURRENCY_TRY = 'TRY';
    public const CURRENCY_UAH = 'UAH';
    public const CURRENCY_VND = 'VND';
    public const CURRENCY_XOF = 'XOF';
    public const CURRENCY_ZAR = 'ZAR';

    public const PERIOD_CURRENT_MONTH = 'current_month';
    public const PERIOD_WHOLE_YEAR = 'year';
    public const STOPS_ALL = '0';
    public const STOPS_ONLY_ONE = '1';
    public const STOPS_DIRECT = '2';
    public const DISTANCE_KM = 'km';
    public const DISTANCE_MILES = 'ml';
    public const SHOW_MESSAGE = 'show_message';
    public const SHOW_SEARCH_FROM = 'show_search_form';
    public const WIDGET_TYPE_TILE = 'brickwork';
    public const WIDGET_TYPE_SLIDER = 'slider';
    public const WIDGET_DESIGN_FULL = 'full';
    public const WIDGET_DESIGN_COMPACT = 'compact';

    public const STRETCH_WIDTH_YES = '1';
    public const STRETCH_WIDTH_NO = '0';

    public const FONT_SIZE_UNIT = 'px';

    public static function stops_number()
    {
        return [
            self::STOPS_ALL => Travelpayouts::__('All'),
            self::STOPS_ONLY_ONE => Travelpayouts::__('No more than one stop'),
            self::STOPS_DIRECT => Travelpayouts::__('Direct'),
        ];
    }

    public static function widget_type()
    {
        return [
            self::WIDGET_TYPE_TILE => Travelpayouts::__('Tile'),
            self::WIDGET_TYPE_SLIDER => Travelpayouts::__('Slider'),
        ];
    }

    public static function widget_design()
    {
        return [
            self::WIDGET_DESIGN_FULL => Travelpayouts::__('Full'),
            self::WIDGET_DESIGN_COMPACT => Travelpayouts::__('Compact'),
        ];
    }

    public static function price_for_period()
    {
        return [
            self::PERIOD_CURRENT_MONTH => Travelpayouts::__('Current month only'),
            self::PERIOD_WHOLE_YEAR => Travelpayouts::__('Whole year'),
            // TODO format Y-m-d
            'december' => Travelpayouts::__('December'),
            'january' => Travelpayouts::__('January'),
            'february' => Travelpayouts::__('February'),
            'march' => Travelpayouts::__('March'),
            'april' => Travelpayouts::__('April'),
            'may' => Travelpayouts::__('May'),
            'june' => Travelpayouts::__('June'),
            'july' => Travelpayouts::__('July'),
            'august' => Travelpayouts::__('August'),
            'september' => Travelpayouts::__('September'),
            'october' => Travelpayouts::__('October'),
            'november' => Travelpayouts::__('November'),
        ];
    }

    public static function flight_sources()
    {
        return [
            self::FLIGHTS_SOURCE_AVIASALES_RU => 'aviasales.ru',
            self::FLIGHTS_SOURCE_AVIASALES_BY => 'aviasales.by',
            self::FLIGHTS_SOURCE_AVIASALES_KG => 'aviasales.kg',
            self::FLIGHTS_SOURCE_AVIASALES_KZ => 'aviasales.kz',
            self::FLIGHTS_SOURCE_AVIASALES_UZ => 'aviasales.uz',
            self::FLIGHTS_SOURCE_AVIASALES_UA => 'aviasales.ua',
            self::FLIGHTS_SOURCE_AVIASALES_COM => 'aviasales.com',
            self::FLIGHTS_SOURCE_JETRADAR_COM => 'jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_COM_BR => 'jetradar.com.br',
            self::FLIGHTS_SOURCE_CA_JETRADAR_COM => 'ca.jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_CH => 'jetradar.ch',
            self::FLIGHTS_SOURCE_JETRADAR_AT => 'jetradar.at',
            self::FLIGHTS_SOURCE_JETRADAR_BE => 'jetradar.be',
            self::FLIGHTS_SOURCE_JETRADAR_CO_NL => 'jetradar.co.nl',
            self::FLIGHTS_SOURCE_JETRADAR_GR => 'jetradar.gr',
            self::FLIGHTS_SOURCE_JETRADAR_COM_AU => 'jetradar.com.au',
            self::FLIGHTS_SOURCE_JETRADAR_DE => 'jetradar.de',
            self::FLIGHTS_SOURCE_JETRADAR_ES => 'jetradar.es',
            self::FLIGHTS_SOURCE_JETRADAR_FR => 'jetradar.fr',
            self::FLIGHTS_SOURCE_JETRADAR_IT => 'jetradar.it',
            self::FLIGHTS_SOURCE_JETRADAR_PT_ => 'jetradar.pt',
            self::FLIGHTS_SOURCE_IE_JETRADAR_COM_ => 'ie.jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_CO_UK => 'jetradar.co.uk',
            self::FLIGHTS_SOURCE_JETRADAR_HK_ => 'jetradar.hk',
            self::FLIGHTS_SOURCE_JETRADAR_IN_ => 'jetradar.in',
            self::FLIGHTS_SOURCE_JETRADAR_CO_NZ => 'jetradar.co.nz',
            self::FLIGHTS_SOURCE_JETRADAR_PH => 'jetradar.ph',
            self::FLIGHTS_SOURCE_JETRADAR_PL_ => 'jetradar.pl',
            self::FLIGHTS_SOURCE_JETRADAR_SG_ => 'jetradar.sg',
            self::FLIGHTS_SOURCE_JETRADAR_CO_TH => 'jetradar.co.th',
        ];
    }


    public static function cache_time_key_flights()
    {
        return 'cache_value_flights';
    }

    public static function cache_time_key_hotels()
    {
        return 'cache_value_hotels';
    }

    public static function table_widget_currencies()
    {
        return [
            self::CURRENCY_RUB => Travelpayouts::__('RUB'),
            self::CURRENCY_USD => Travelpayouts::__('USD'),
            self::CURRENCY_EUR => Travelpayouts::__('EUR'),
            self::CURRENCY_AED => Travelpayouts::__('AED'),
            self::CURRENCY_AMD => Travelpayouts::__('AMD'),
            self::CURRENCY_ARS => Travelpayouts::__('ARS'),
            self::CURRENCY_AUD => Travelpayouts::__('AUD'),
            self::CURRENCY_AZN => Travelpayouts::__('AZN'),
            self::CURRENCY_BDT => Travelpayouts::__('BDT'),
            self::CURRENCY_BGN => Travelpayouts::__('BGN'),
            self::CURRENCY_BHD => Travelpayouts::__('BHD'),
            self::CURRENCY_BRL => Travelpayouts::__('BRL'),
            self::CURRENCY_BYN => Travelpayouts::__('BYN'),
            self::CURRENCY_CAD => Travelpayouts::__('CAD'),
            self::CURRENCY_CHF => Travelpayouts::__('CHF'),
            self::CURRENCY_CLP => Travelpayouts::__('CLP'),
            self::CURRENCY_CNY => Travelpayouts::__('CNY'),
            self::CURRENCY_COP => Travelpayouts::__('COP'),
            self::CURRENCY_CZK => Travelpayouts::__('CZK'),
            self::CURRENCY_DKK => Travelpayouts::__('DKK'),
            self::CURRENCY_EGP => Travelpayouts::__('EGP'),
            self::CURRENCY_GBP => Travelpayouts::__('GBP'),
            self::CURRENCY_GEL => Travelpayouts::__('GEL'),
            self::CURRENCY_HKD => Travelpayouts::__('HKD'),
            self::CURRENCY_HUF => Travelpayouts::__('HUF'),
            self::CURRENCY_IDR => Travelpayouts::__('IDR'),
            self::CURRENCY_ILS => Travelpayouts::__('ILS'),
            self::CURRENCY_INR => Travelpayouts::__('INR'),
            self::CURRENCY_IQD => Travelpayouts::__('IQD'),
            self::CURRENCY_ISK => Travelpayouts::__('ISK'),
            self::CURRENCY_JOD => Travelpayouts::__('JOD'),
            self::CURRENCY_JPY => Travelpayouts::__('JPY'),
            self::CURRENCY_KGS => Travelpayouts::__('KGS'),
            self::CURRENCY_KRW => Travelpayouts::__('KRW'),
            self::CURRENCY_KWD => Travelpayouts::__('KWD'),
            self::CURRENCY_KZT => Travelpayouts::__('KZT'),
            self::CURRENCY_LKR => Travelpayouts::__('LKR'),
            self::CURRENCY_LYD => Travelpayouts::__('LYD'),
            self::CURRENCY_MUR => Travelpayouts::__('MUR'),
            self::CURRENCY_MXN => Travelpayouts::__('MXN'),
            self::CURRENCY_MYR => Travelpayouts::__('MYR'),
            self::CURRENCY_NGN => Travelpayouts::__('NGN'),
            self::CURRENCY_NOK => Travelpayouts::__('NOK'),
            self::CURRENCY_NPR => Travelpayouts::__('NPR'),
            self::CURRENCY_NZD => Travelpayouts::__('NZD'),
            self::CURRENCY_OMR => Travelpayouts::__('OMR'),
            self::CURRENCY_PEN => Travelpayouts::__('PEN'),
            self::CURRENCY_PHP => Travelpayouts::__('PHP'),
            self::CURRENCY_PKR => Travelpayouts::__('PKR'),
            self::CURRENCY_PLN => Travelpayouts::__('PLN'),
            self::CURRENCY_QAR => Travelpayouts::__('QAR'),
            self::CURRENCY_RON => Travelpayouts::__('RON'),
            self::CURRENCY_RSD => Travelpayouts::__('RSD'),
            self::CURRENCY_SAR => Travelpayouts::__('SAR'),
            self::CURRENCY_SEK => Travelpayouts::__('SEK'),
            self::CURRENCY_SGD => Travelpayouts::__('SGD'),
            self::CURRENCY_THB => Travelpayouts::__('THB'),
            self::CURRENCY_TJS => Travelpayouts::__('TJS'),
            self::CURRENCY_TRY => Travelpayouts::__('TRY'),
            self::CURRENCY_UAH => Travelpayouts::__('UAH'),
            self::CURRENCY_VND => Travelpayouts::__('VND'),
            self::CURRENCY_XOF => Travelpayouts::__('XOF'),
            self::CURRENCY_ZAR => Travelpayouts::__('ZAR'),
        ];
    }

    public static function getDefaultCurrencyCode()
    {
        if (LanguageHelper::isRuDashboard()) {
            return self::CURRENCY_RUB;
        }

        return self::CURRENCY_USD;
    }

    public static function script_locations_values($key)
    {
        $locations = [
            'in_header' => false,
            'in_footer' => true,
        ];

        if (!isset($locations[$key])) {
            return true;
        }

        return $locations[$key];
    }

}
