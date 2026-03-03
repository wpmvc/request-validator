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

        // Check if it's a file first
        if ( in_array( 'file', $this->validator->explode_rules ?? [] ) ) {
            $files = $this->validator->wp_rest_request->get_file_params();
            if ( empty( $files[$attribute]['size'] ) ) {
                return true;
            }
            return ( $files[$attribute]['size'] / 1024 ) == $this->size;
        }

        if ( in_array( 'numeric', $this->validator->explode_rules ?? [] ) || in_array( 'integer', $this->validator->explode_rules ?? [] ) ) {
            return (float) $value == $this->size;
        }

        if ( is_array( $value ) || in_array( 'array', $this->validator->explode_rules ?? [] ) ) {
            return count( $value ) == $this->size;
        }

        if ( is_string( $value ) ) {
            return mb_strlen( $value ) == $this->size;
        }

        return false;
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
