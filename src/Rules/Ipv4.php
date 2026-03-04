<?php
/**
 * Ipv4 rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Ipv4
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Ipv4 extends Rule {
    public static function get_name(): string {
        return 'ipv4';
    }

    public function passes( string $attribute, $value ): bool {
        return filter_var( $value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid IPv4 address.', 'wpmvc' ), ':attribute' );
    }
}
