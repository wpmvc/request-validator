<?php
/**
 * Mimetypes rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Mimetypes
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Mimetypes extends Rule {
    protected $allowed_mimetypes;

    public function __construct( array $allowed_mimetypes ) {
        $this->allowed_mimetypes = $allowed_mimetypes;
    }

    public static function get_name(): string {
        return 'mimetypes';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $files = $this->validator->wp_rest_request->get_file_params();

        if ( empty( $files[$attribute] ) || empty( $files[$attribute]['tmp_name'] ) ) {
            return true; // Use 'required' to fail if empty
        }

        $file_mime_type = mime_content_type( $files[$attribute]['tmp_name'] );

        return in_array( $file_mime_type, $this->allowed_mimetypes, true );
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: allowed mimetypes */
        return sprintf( __( 'The %1$s must be a file of type: %2$s.', 'wpmvc' ), ':attribute', ':values' );
    }

    public function replace_placeholders( string $message ): string {
        return str_replace( ':values', implode( ', ', $this->allowed_mimetypes ), $message );
    }

    public function get_allowed_mimetypes() {
        return $this->allowed_mimetypes;
    }
}
