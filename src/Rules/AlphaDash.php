<?php
/**
 * AlphaDash rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class AlphaDash
 *
 * @package WpMVC\RequestValidator\Rules
 */
class AlphaDash extends Rule {
    public static function get_name(): string {
        return 'alpha_dash';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && preg_match( '/^[\pL\pM\pN_-]+$/u', $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must only contain letters, numbers, dashes and underscores.', 'wpmvc' ), ':attribute' );
    }
}
