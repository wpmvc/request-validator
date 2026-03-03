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

        // Check if it's a file first
        if ( in_array( 'file', $this->validator->explode_rules ?? [] ) ) {
            $files = $this->validator->wp_rest_request->get_file_params();
            if ( empty( $files[$attribute]['size'] ) ) {
                return true;
            }
            $size = $files[$attribute]['size'] / 1024;
            return $size >= $this->min && $size <= $this->max;
        }

        if ( in_array( 'numeric', $this->validator->explode_rules ?? [] ) || in_array( 'integer', $this->validator->explode_rules ?? [] ) ) {
            $num = (float) $value;
            return $num >= $this->min && $num <= $this->max;
        }

        if ( is_array( $value ) || in_array( 'array', $this->validator->explode_rules ?? [] ) ) {
            $count = count( $value );
            return $count >= $this->min && $count <= $this->max;
        }

        if ( is_string( $value ) ) {
            $length = mb_strlen( $value );
            return $length >= $this->min && $length <= $this->max;
        }

        return false;
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
