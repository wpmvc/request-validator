<?php
/**
 * Timezone rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Timezone
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Timezone extends Rule {
    public static function get_name(): string {
        return 'timezone';
    }

    public function passes( string $attribute, $value ): bool {
        return in_array( $value, timezone_identifiers_list(), true );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid timezone.', 'wpmvc' ), ':attribute' );
    }
}
