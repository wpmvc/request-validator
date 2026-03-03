<?php
/**
 * StringRule rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class StringRule
 *
 * @package WpMVC\RequestValidator\Rules
 */
class StringRule extends Rule {
    public static function get_name(): string {
        return 'string';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a string.', 'wpmvc' ), ':attribute' );
    }
}
