<?php
/**
 * Max rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Max
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Max extends Rule {
    protected $max;

    public function __construct( $max ) {
        $this->max = $max;
    }

    public static function get_name(): string {
        return 'max';
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
            return ( $files[$attribute]['size'] / 1024 ) <= $this->max;
        }

        if ( in_array( 'numeric', $this->validator->explode_rules ?? [] ) || in_array( 'integer', $this->validator->explode_rules ?? [] ) ) {
            return (float) $value <= $this->max;
        }

        if ( is_array( $value ) || in_array( 'array', $this->validator->explode_rules ?? [] ) ) {
            return count( $value ) <= $this->max;
        }

        if ( is_string( $value ) ) {
            return mb_strlen( $value ) <= $this->max;
        }

        return false;
    }

    protected function default_message(): string {
        $type = $this->get_attribute_type();

        switch ( $type ) {
            case 'numeric':
                /* translators: 1: attribute name, 2: max value */
                return sprintf( __( 'The %1$s must not be greater than %2$s.', 'wpmvc' ), ':attribute', ':max' );
            case 'file':
                /* translators: 1: attribute name, 2: max value */
                return sprintf( __( 'The %1$s must not be greater than %2$s kilobytes.', 'wpmvc' ), ':attribute', ':max' );
            case 'array':
                /* translators: 1: attribute name, 2: max items */
                return sprintf( __( 'The %1$s must not have more than %2$s items.', 'wpmvc' ), ':attribute', ':max' );
            default:
                /* translators: 1: attribute name, 2: max characters */
                return sprintf( __( 'The %1$s must not be greater than %2$s characters.', 'wpmvc' ), ':attribute', ':max' );
        }
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':max', $this->max, $message );
    }

    public function get_max() {
        return $this->max;
    }
}
