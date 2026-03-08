<?php
/**
 * Email rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Email
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Email extends Rule {
    public static function get_name(): string {
        return 'email';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && is_email( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid email address.', 'wpmvc' ), ':attribute' );
    }
}
