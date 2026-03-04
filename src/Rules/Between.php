<?php
/**
 * Between rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Between
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Between extends Rule {
    protected $min;

    protected $max;

    public function __construct( $min, $max ) {
        $this->min = $min;
        $this->max = $max;
    }

    public static function get_name(): string {
        return 'between';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $rules = $this->validator->get_attribute_rules( $attribute );

        // 1. Check if it's a file
        $files = $this->validator->wp_rest_request->get_file_params();
        if ( isset( $files[$attribute] ) && is_array( $files[$attribute] ) && isset( $files[$attribute]['tmp_name'] ) ) {
            if ( empty( $files[$attribute]['size'] ) ) {
                return true;
            }
            $size = $files[$attribute]['size'] / 1024;
            return $size >= $this->min && $size <= $this->max;
        }

        // 2. Check if it's numeric
        if ( is_numeric( $value ) && ( in_array( 'numeric', $rules, true ) || in_array( 'integer', $rules, true ) ) ) {
            $num = (float) $value;
            return $num >= $this->min && $num <= $this->max;
        }

        // 3. Check if it's an array
        if ( is_array( $value ) || in_array( 'array', $rules, true ) ) {
            $count = is_countable( $value ) ? count( $value ) : 0;
            if ( ! is_array( $value ) && ! is_countable( $value ) ) {
                $count = 0;
            }
            return $count >= $this->min && $count <= $this->max;
        }

        // 4. Fallback to string length
        $length = mb_strlen( (string) $value );
        return $length >= $this->min && $length <= $this->max;
    }

    protected function default_message(): string {
        $type = $this->get_attribute_type();

        switch ( $type ) {
            case 'numeric':
                /* translators: 1: attribute name, 2: min value, 3: max value */
                return sprintf( __( 'The %1$s must be between %2$s and %3$s.', 'wpmvc' ), ':attribute', ':min', ':max' );
            case 'file':
                /* translators: 1: attribute name, 2: min value, 3: max value */
                return sprintf( __( 'The %1$s must be between %2$s and %3$s kilobytes.', 'wpmvc' ), ':attribute', ':min', ':max' );
            case 'array':
                /* translators: 1: attribute name, 2: min items, 3: max items */
                return sprintf( __( 'The %1$s must have between %2$s and %3$s items.', 'wpmvc' ), ':attribute', ':min', ':max' );
            default:
                /* translators: 1: attribute name, 2: min characters, 3: max characters */
                return sprintf( __( 'The %1$s must be between %2$s and %3$s characters.', 'wpmvc' ), ':attribute', ':min', ':max' );
        }
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
