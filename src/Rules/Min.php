<?php
/**
 * Min rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Min
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Min extends Rule {
    protected $min;

    public function __construct( $min ) {
        $this->min = $min;
    }

    public static function get_name(): string {
        return 'min';
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
            return ( $files[$attribute]['size'] / 1024 ) >= $this->min;
        }

        // 2. Check if it's numeric
        if ( is_numeric( $value ) && in_array( 'numeric', $rules, true ) || in_array( 'integer', $rules, true ) ) {
            return (float) $value >= $this->min;
        }

        // 3. Check if it's an array
        if ( is_array( $value ) || in_array( 'array', $rules, true ) ) {
            return is_countable( $value ) && count( $value ) >= $this->min;
        }

        // 4. Fallback to string length
        return mb_strlen( (string) $value ) >= $this->min;
    }

    protected function default_message(): string {
        $type = $this->get_attribute_type();

        switch ( $type ) {
            case 'numeric':
                /* translators: 1: attribute name, 2: min value */
                return sprintf( __( 'The %1$s must be at least %2$s.', 'wpmvc' ), ':attribute', ':min' );
            case 'file':
                /* translators: 1: attribute name, 2: min value */
                return sprintf( __( 'The %1$s must be at least %2$s kilobytes.', 'wpmvc' ), ':attribute', ':min' );
            case 'array':
                /* translators: 1: attribute name, 2: min items */
                return sprintf( __( 'The %1$s must have at least %2$s items.', 'wpmvc' ), ':attribute', ':min' );
            default:
                /* translators: 1: attribute name, 2: min characters */
                return sprintf( __( 'The %1$s must be at least %2$s characters.', 'wpmvc' ), ':attribute', ':min' );
        }
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':min', $this->min, $message );
    }

    public function get_min() {
        return $this->min;
    }
}
