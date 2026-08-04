<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;

use Travelpayouts;
use Travelpayouts\components\tables\BaseColumnLabels;

/***
 * Class ColumnLabels
 * @package Travelpayouts\modules\tables\components
 */
class ColumnLabels extends BaseColumnLabels
{
    public const DEPARTURE_AT = 'departure_at';
    public const NUMBER_OF_CHANGES = 'number_of_changes';
    public const BUTTON = 'button';
    public const PRICE = 'price';
    public const TRIP_CLASS = 'trip_class';
    public const DISTANCE = 'distance';
    public const RETURN_AT = 'return_at';
    public const AIRLINE_LOGO = 'airline_logo';
    public const FLIGHT_NUMBER = 'flight_number';
    public const FLIGHT = 'flight';
    public const AIRLINE = 'airline';
    public const DESTINATION = 'destination';
    public const ORIGIN_DESTINATION = 'origin_destination';
    public const PLACE = 'place';
    public const DIRECTION = 'direction';
    public const ORIGIN = 'origin';
    public const FOUND_AT = 'found_at';
    public const PRICE_DISTANCE = 'price_distance';
    public const TIME_AND_STOPS = 'time_and_stops';
    public const ROUTE = 'route';
    public const SCHEDULE = 'schedule';
    public const FULL_AIRLINE_LOGO = 'full_airline_logo';
    public const COMPACT_AIRLINE_LOGO = 'compact_airline_logo';
    public const AIRLINE_NAME = 'airline_name_string';

    public function translationKeys()
    {
        return [
            self::DEPARTURE_AT => 'flights.departure_at',
            self::NUMBER_OF_CHANGES => 'flights.number_of_changes',
            self::BUTTON => 'flights.button_column_title',
            self::PRICE => 'flights.price',
            self::TRIP_CLASS => 'flights.trip_class',
            self::DISTANCE => 'flights.distance',
            self::RETURN_AT => 'flights.return_at',
            self::AIRLINE_LOGO => 'flights.airline_logo',
            self::FLIGHT_NUMBER => 'flights.flight_number',
            self::FLIGHT => 'flights.flight',
            self::AIRLINE => 'flights.airline',
            self::DESTINATION => 'flights.destination',
            self::ORIGIN_DESTINATION => 'flights.origin_destination',
            self::PLACE => 'flights.place',
            self::DIRECTION => 'flights.direction',
            self::ORIGIN => 'flights.origin',
            self::FOUND_AT => 'flights.found_at',
            self::PRICE_DISTANCE => 'flights.price_distance',
            self::TIME_AND_STOPS => 'flights.time_and_stops',
            self::ROUTE => 'flights.route',
            self::SCHEDULE => 'flights.schedule',
            self::COMPACT_AIRLINE_LOGO => 'flights.airline',
            self::FULL_AIRLINE_LOGO => 'flights.airline',
            self::AIRLINE_NAME => 'flights.airline',
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultTranslations()
    {
        return [
            self::DEPARTURE_AT => Travelpayouts::__('Departure date'),
            self::NUMBER_OF_CHANGES => Travelpayouts::__('Stops'),
            self::BUTTON => Travelpayouts::__('Button'),
            self::PRICE => Travelpayouts::__('Price'),
            self::TRIP_CLASS => Travelpayouts::__('Flight class'),
            self::DISTANCE => Travelpayouts::__('Distance'),
            self::RETURN_AT => Travelpayouts::__('Return date'),
            self::AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::FLIGHT_NUMBER => Travelpayouts::__('Flight number'),
            self::FLIGHT => Travelpayouts::__('Flight'),
            self::AIRLINE => Travelpayouts::__('Airline name'),
            self::AIRLINE_NAME => Travelpayouts::__('Airline name'),
            self::FULL_AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::COMPACT_AIRLINE_LOGO => Travelpayouts::__('Compact airline logo with name'),
            self::DESTINATION => Travelpayouts::__('Destination'),
            self::ORIGIN_DESTINATION => Travelpayouts::__('Origin - Destination'),
            self::PLACE => Travelpayouts::__('Rank'),
            self::DIRECTION => Travelpayouts::__('Direction'),
            self::ORIGIN => Travelpayouts::__('Origin'),
            self::FOUND_AT => Travelpayouts::__('When found'),
            self::PRICE_DISTANCE => Travelpayouts::__('Price/distance'),
            self::TIME_AND_STOPS => Travelpayouts::__('Time and stops'),
            self::ROUTE => Travelpayouts::__('Route'),
            self::SCHEDULE => Travelpayouts::__('Schedule'),
        ];
    }

    public function getDashboardColumnLabels(array $names = null): array
    {
        $labelsList = array_merge(parent::getDashboardColumnLabels($names), [
            self::AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::FULL_AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::COMPACT_AIRLINE_LOGO => Travelpayouts::__('Compact airline logo with name'),
        ]);

        if (is_array($names)) {
            $result = [];
            foreach ($names as $key) {
                $result[$key] = array_key_exists($key, $labelsList) ? $labelsList[$key] : null;
            }
            return $result;
        }

        return $labelsList;
    }

}
