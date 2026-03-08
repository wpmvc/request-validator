<?php
/**
 * Uuid rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Uuid
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Uuid extends Rule {
    public static function get_name(): string {
        return 'uuid';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && wp_is_uuid( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid UUID.', 'wpmvc' ), ':attribute' );
    }
}
