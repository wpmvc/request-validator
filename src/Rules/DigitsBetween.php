<?php
/**
 * DigitsBetween rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class DigitsBetween
 *
 * @package WpMVC\RequestValidator\Rules
 */
class DigitsBetween extends Rule {
    protected $min;

    protected $max;

    public function __construct( $min, $max ) {
        $this->min = $min;
        $this->max = $max;
    }

    public static function get_name(): string {
        return 'digits_between';
    }

    public function passes( string $attribute, $value ): bool {
        $length = strlen( (string) $value );
        return is_numeric( $value ) && $length >= $this->min && $length <= $this->max;
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: min digits, 3: max digits */
        return sprintf( __( 'The %1$s must be between %2$s and %3$s digits.', 'wpmvc' ), ':attribute', ':min', ':max' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( [':min', ':max'], [$this->min, $this->max], $message );
    }

    public function get_min() {
        return $this->min;
    }

    public function get_max() {
        return $this->max;
    }
}
