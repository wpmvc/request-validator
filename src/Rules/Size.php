<?php
/**
 * Size rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Size
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Size extends Rule {
    protected $size;

    public function __construct( $size ) {
        $this->size = $size;
    }

    public static function get_name(): string {
        return 'size';
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
            return ( $files[$attribute]['size'] / 1024 ) == $this->size;
        }

        // 2. Check if it's numeric
        if ( is_numeric( $value ) && ( in_array( 'numeric', $rules, true ) || in_array( 'integer', $rules, true ) ) ) {
            return (float) $value == $this->size;
        }

        // 3. Check if it's an array
        if ( is_array( $value ) || in_array( 'array', $rules, true ) ) {
            return is_countable( $value ) && count( $value ) == $this->size;
        }

        // 4. Fallback to string length
        return mb_strlen( (string) $value ) == $this->size;
    }

    protected function default_message(): string {
        $type = $this->get_attribute_type();

        switch ( $type ) {
            case 'numeric':
                /* translators: 1: attribute name, 2: size */
                return sprintf( __( 'The %1$s must be %2$s.', 'wpmvc' ), ':attribute', ':size' );
            case 'file':
                /* translators: 1: attribute name, 2: size */
                return sprintf( __( 'The %1$s must be %2$s kilobytes.', 'wpmvc' ), ':attribute', ':size' );
            case 'array':
                /* translators: 1: attribute name, 2: size */
                return sprintf( __( 'The %1$s must contain %2$s items.', 'wpmvc' ), ':attribute', ':size' );
            default:
                /* translators: 1: attribute name, 2: size */
                return sprintf( __( 'The %1$s must be %2$s characters.', 'wpmvc' ), ':attribute', ':size' );
        }
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':size', $this->size, $message );
    }

    public function get_size() {
        return $this->size;
    }
}
