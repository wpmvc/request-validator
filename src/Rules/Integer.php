<?php
/**
 * Integer rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Integer
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Integer extends Rule {
    public static function get_name(): string {
        return 'integer';
    }

    public function passes( string $attribute, $value ): bool {
        return filter_var( $value, FILTER_VALIDATE_INT ) !== false;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be an integer.', 'wpmvc' ), ':attribute' );
    }
}
