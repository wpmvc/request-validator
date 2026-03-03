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

        // Check if it's a file first
        if ( in_array( 'file', $this->validator->explode_rules ?? [] ) ) {
            $files = $this->validator->wp_rest_request->get_file_params();
            if ( empty( $files[$attribute]['size'] ) ) {
                return true;
            }
            return ( $files[$attribute]['size'] / 1024 ) >= $this->min;
        }

        if ( in_array( 'numeric', $this->validator->explode_rules ?? [] ) || in_array( 'integer', $this->validator->explode_rules ?? [] ) ) {
            return (float) $value >= $this->min;
        }

        if ( is_array( $value ) || in_array( 'array', $this->validator->explode_rules ?? [] ) ) {
            if ( ! is_countable( $value ) ) {
                return false;
            }
            return count( $value ) >= $this->min;
        }

        if ( is_string( $value ) ) {
            return mb_strlen( $value ) >= $this->min;
        }

        return false;
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
