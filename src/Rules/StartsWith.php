<?php
/**
 * StartsWith rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class StartsWith
 *
 * @package WpMVC\RequestValidator\Rules
 */
class StartsWith extends Rule {
    protected array $prefixes;

    public function __construct( array $prefixes ) {
        $this->prefixes = $prefixes;
    }

    public static function get_name(): string {
        return 'starts_with';
    }

    public function passes( string $attribute, $value ): bool {
        foreach ( $this->prefixes as $prefix ) {
            if ( strpos( $value, $prefix ) === 0 ) {
                return true;
            }
        }
        return false;
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: list of prefixes */
        return sprintf( __( 'The %1$s must start with one of the following: %2$s.', 'wpmvc' ), ':attribute', ':values' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':values', implode( ', ', $this->prefixes ), $message );
    }
}
