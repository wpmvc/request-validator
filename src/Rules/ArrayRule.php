<?php
/**
 * ArrayRule rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class ArrayRule
 *
 * @package WpMVC\RequestValidator\Rules
 */
class ArrayRule extends Rule {
    public static function get_name(): string {
        return 'array';
    }

    public function passes( string $attribute, $value ): bool {
        return is_array( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be an array.', 'wpmvc' ), ':attribute' );
    }
}
