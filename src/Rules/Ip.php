<?php
/**
 * Ip rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Ip
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Ip extends Rule {
    public static function get_name(): string {
        return 'ip';
    }

    public function passes( string $attribute, $value ): bool {
        return filter_var( $value, FILTER_VALIDATE_IP ) !== false;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid IP address.', 'wpmvc' ), ':attribute' );
    }
}
