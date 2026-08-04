<?php

/**
 * Redux_Travelpayouts Descriptor Types Class
 *
 * @class Redux_Travelpayouts_Descriptor_Types
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework
 * @author Tofandel
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class Redux_Travelpayouts_Descriptor_Types
 */
abstract class Redux_Travelpayouts_Descriptor_Types
{
    public const TEXT     = 'text';
    public const TEXTAREA = 'textarea';
    public const BOOL     = 'bool';
    public const SLIDER   = 'slider';
    public const NUMBER   = 'number';
    public const RANGE    = 'range';
    public const OPTIONS  = 'array';
    public const WP_DATA  = 'wp_data';
    public const RADIO    = 'radio';
    // Todo add more field types for the builder!

    /**
     * Get the available types of field.
     *
     * @return array
     */
    public static function get_types()
    {
        static $const_cache;

        if (! isset($const_cache)) {
            $reflect     = new ReflectionClass(__CLASS__);
            $const_cache = $reflect->getConstants();
        }

        return $const_cache;
    }


    /**
     * Check if a type is in the list of available types.
     *
     * @param string $value Check if it's a valid type.
     *
     * @return bool
     */
    public static function is_valid_type($value)
    {
        return in_array($value, self::get_types(), true);
    }

}
