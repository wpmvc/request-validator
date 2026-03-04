<?php
/**
 * Boolean rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Boolean
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Boolean extends Rule {
    public static function get_name(): string {
        return 'boolean';
    }

    public function passes( string $attribute, $value ): bool {
        return in_array( $value, [true, false, 1, 0, '1', '0', 'true', 'false'], true );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s field must be true or false.', 'wpmvc' ), ':attribute' );
    }
}
