<?php
/**
 * DateEquals rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

use WpMVC\RequestValidator\DateTime;

/**
 * Class DateEquals
 *
 * @package WpMVC\RequestValidator\Rules
 */
class DateEquals extends Rule {
    use DateTime;

    protected $date;

    public function __construct( $date, $format = 'Y-m-d' ) {
        $this->date   = $date;
        $this->format = $format;
    }

    public static function get_name(): string {
        return 'date_equals';
    }

    public function passes( string $attribute, $value ): bool {
        if ( empty( $value ) || ! $this->is_it_valid_date( $value, $this->format ) ) {
            return false;
        }

        $timestamp       = $this->get_timestamp( $this->date, $this->format );
        $input_timestamp = $this->get_timestamp( $value, $this->format );

        return $input_timestamp === $timestamp;
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: date */
        return sprintf( __( 'The %1$s must be a date equal to %2$s.', 'wpmvc' ), ':attribute', ':date' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':date', $this->date, $message );
    }
}
