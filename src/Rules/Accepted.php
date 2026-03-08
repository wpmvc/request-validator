<?php
/**
 * Accepted rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Accepted
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Accepted extends Rule {
    public static function get_name(): string {
        return 'accepted';
    }

    public function passes( string $attribute, $value ): bool {
        return in_array( $value, [ 'yes', 'on', '1', 'true', 1, true ], true );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be accepted.', 'wpmvc' ), ':attribute' );
    }
}
