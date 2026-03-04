<?php
/**
 * MacAddress rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class MacAddress
 *
 * @package WpMVC\RequestValidator\Rules
 */
class MacAddress extends Rule {
    public static function get_name(): string {
        return 'mac_address';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && preg_match( '/^([0-9a-fA-F]{2}[:.-]){5}[0-9a-fA-F]{2}$/', $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid MAC address.', 'wpmvc' ), ':attribute' );
    }
}
