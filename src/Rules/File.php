<?php
/**
 * File rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class File
 *
 * @package WpMVC\RequestValidator\Rules
 */
class File extends Rule {
    public static function get_name(): string {
        return 'file';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $files = $this->validator->wp_rest_request->get_file_params();

        return ! empty( $files[$attribute] ) && $files[$attribute]['error'] === UPLOAD_ERR_OK;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s must be a file.', 'wpmvc' ), ':attribute' );
    }
}
