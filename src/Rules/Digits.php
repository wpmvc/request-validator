<?php
/**
 * Digits rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Digits
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Digits extends Rule {
    protected $digits;

    public function __construct( $digits ) {
        $this->digits = $digits;
    }

    public static function get_name(): string {
        return 'digits';
    }

    public function passes( string $attribute, $value ): bool {
        return is_numeric( $value ) && strlen( (string) $value ) === (int) $this->digits;
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: digits count */
        return sprintf( __( 'The %1$s must be %2$s digits.', 'wpmvc' ), ':attribute', ':digits' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':digits', $this->digits, $message );
    }

    public function get_digits() {
        return $this->digits;
    }
}
