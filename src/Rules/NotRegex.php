<?php
/**
 * NotRegex rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class NotRegex
 *
 * @package WpMVC\RequestValidator\Rules
 */
class NotRegex extends Rule {
    protected string $pattern;

    public function __construct( string $pattern ) {
        $this->pattern = $pattern;
    }

    public static function get_name(): string {
        return 'not_regex';
    }

    public function passes( string $attribute, $value ): bool {
        return is_string( $value ) && ! preg_match( $this->pattern, $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s format is invalid.', 'wpmvc' ), ':attribute' );
    }
}
