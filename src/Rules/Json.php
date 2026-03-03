<?php
/**
 * Json rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Json
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Json extends Rule {
    public static function get_name(): string {
        return 'json';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! is_string( $value ) ) {
            return false;
        }
        json_decode( $value );
        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid JSON string.', 'wpmvc' ), ':attribute' );
    }
}
