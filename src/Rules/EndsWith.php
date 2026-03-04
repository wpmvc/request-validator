<?php
/**
 * EndsWith rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class EndsWith
 *
 * @package WpMVC\RequestValidator\Rules
 */
class EndsWith extends Rule {
    protected array $suffixes;

    public function __construct( array $suffixes ) {
        $this->suffixes = $suffixes;
    }

    public static function get_name(): string {
        return 'ends_with';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! is_string( $value ) ) {
            return false;
        }
        foreach ( $this->suffixes as $suffix ) {
            if ( substr( $value, -strlen( $suffix ) ) === $suffix ) {
                return true;
            }
        }
        return false;
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: list of suffixes */
        return sprintf( __( 'The %1$s must end with one of the following: %2$s.', 'wpmvc' ), ':attribute', ':values' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':values', implode( ', ', $this->suffixes ), $message );
    }
}
