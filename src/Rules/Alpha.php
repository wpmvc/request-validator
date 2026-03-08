<?php
/**
 * Alpha rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Alpha
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Alpha extends Rule {
    public static function get_name(): string {
        return 'alpha';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && preg_match( '/^[\pL\pM]+$/u', $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must only contain letters.', 'wpmvc' ), ':attribute' );
    }
}
