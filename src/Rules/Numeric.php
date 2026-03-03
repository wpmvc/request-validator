<?php
/**
 * Numeric rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Numeric
 *
 * @package WpMVC\RequestValidator\Rules
 */
// phpcs:ignore PHPCompatibility.Keywords.ForbiddenNamesAsDeclared.numericFound
class Numeric extends Rule {
    public static function get_name(): string {
        return 'numeric';
    }

    public function passes( string $attribute, $value ): bool {
        return is_numeric( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a number.', 'wpmvc' ), ':attribute' );
    }
}
