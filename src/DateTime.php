<?php
/**
 * DateTime trait.
 *
 * @package WpMVC\RequestValidator
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

use DateTime as PhpDateTime;

/**
 * Trait DateTime
 *
 * Provides date and time validation and parsing utilities.
 *
 * @package WpMVC\RequestValidator
 */
trait DateTime {
    /**
     * The date/time format to use.
     *
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * Determine if a date string is valid for a given format.
     *
     * @param  string  $date
     * @param  string  $format
     * @return bool
     */
    private function is_it_valid_date( $date, string $format ) {
        if ( ! is_string( $date ) ) {
            return false;
        }
        $input_date = PhpDateTime::createFromFormat( $format, $date );
        return $input_date && $input_date->format( $format ) === $date;
    }

    /**
     * Get the Unix timestamp for a given date string and format.
     *
     * @param  string  $date
     * @param  string  $format
     * @return int
     */
    private function get_timestamp( string $date, string $format ) {
        $request = property_exists( $this, 'validator' ) && $this->validator ? $this->validator->wp_rest_request : $this->wp_rest_request;

        // If the date string matches a parameter in the request, use its value
        if ( $request->has_param( $date ) ) {
            $date = $request->get_param( $date );
        }

        $dt = PhpDateTime::createFromFormat( $format, $date );
        
        if ( ! $dt ) {
            return 0;
        }

        // If the format doesn't include time components, createFromFormat might set them to current time or '?'
        // We should ensure a consistent start-of-day for formats without time.
        if ( strpos( $format, 'H' ) === false && strpos( $format, 'h' ) === false ) {
            $dt->setTime( 0, 0, 0 );
        }

        return $dt->getTimestamp();
    }
}