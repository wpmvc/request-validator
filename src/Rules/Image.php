<?php
/**
 * Image rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Image
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Image extends Rule {
    public static function get_name(): string {
        return 'image';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $files = $this->validator->wp_rest_request->get_file_params();

        if ( empty( $files[$attribute] ) ) {
            return true; // Use 'required' to fail if empty
        }

        $allowed_extensions = [ 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp' ];
        
        return $this->validator->validate_mime( $files[$attribute], implode( ',', $allowed_extensions ) );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be an image.', 'wpmvc' ), ':attribute' );
    }
}
