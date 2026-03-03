<?php
/**
 * Url rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Url
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Url extends Rule {
    public static function get_name(): string {
        return 'url';
    }

    public function passes( string $attribute, $value ): bool {
        return $value === null || filter_var( $value, FILTER_VALIDATE_URL ) !== false;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a valid URL.', 'wpmvc' ), ':attribute' );
    }
}
