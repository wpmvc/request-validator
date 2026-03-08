<?php
/**
 * HasMime trait.
 *
 * @package WpMVC\RequestValidator
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

/**
 * Trait HasMime
 *
 * Provides MIME type validation utilities.
 *
 * @package WpMVC\RequestValidator
 */
trait HasMime {
    /**
     * Cache for the mimes list.
     * 
     * @var array|null
     */
    protected static ?array $mimes_cache = null;

    /**
     * Validate a file's MIME type.
     *
     * @param  array   $file
     * @param  string  $allowed_mimes
     * @return bool
     */
    public function validate_mime( array $file, string $allowed_mimes ): bool {
        if ( empty( $file['tmp_name'] ) || empty( $file['name'] ) ) {
            return false;
        }

        $allowed_mimes   = explode( ',', $allowed_mimes );
        $file_mime_type  = mime_content_type( $file['tmp_name'] );
        $available_mimes = array_keys( $this->get_mimes_list(), $file_mime_type );
        $file_extension  = pathinfo( $file['name'], PATHINFO_EXTENSION );

        foreach ( $allowed_mimes as $allowed_mime ) {
            if ( in_array( $allowed_mime, $available_mimes ) && $allowed_mime === $file_extension ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the list of supported mimes.
     * 
     * @return array
     */
    protected function get_mimes_list(): array {
        if ( self::$mimes_cache === null ) {
            self::$mimes_cache = require __DIR__ . '/mimes.php';
        }

        return self::$mimes_cache;
    }
}