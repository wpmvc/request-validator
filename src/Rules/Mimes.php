<?php
/**
 * Mimes rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Mimes
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Mimes extends Rule {
    protected $allowed_extensions;

    public function __construct( array $allowed_extensions ) {
        $this->allowed_extensions = $allowed_extensions;
    }

    public static function get_name(): string {
        return 'mimes';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $files = $this->validator->wp_rest_request->get_file_params();

        if ( empty( $files[$attribute] ) ) {
            return true;
        }

        return $this->validator->validate_mime( $files[$attribute], implode( ',', $this->allowed_extensions ) );
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: allowed mimes */
        return sprintf( __( 'The %1$s must be a file of type: %2$s.', 'wpmvc' ), ':attribute', ':values' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':values', implode( ', ', $this->allowed_extensions ), $message );
    }
}
