<?php
/**
 * Required rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Required
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Required extends Rule {
    public static function get_name(): string {
        return 'required';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return ! empty( $value );
        }

        if ( ! $this->validator->data_has( $attribute ) ) {
            return false;
        }

        // ISSUE FIX: Previously, this rule only checked for the existence of the key in the data.
        // It now correctly validates that the value is actually non-empty (Laravel-style).
        // This ensures nested 'required' rules work as expected when the key exists but is empty.
        if ( is_array( $value ) ) {
            return count( $value ) > 0;
        }

        if ( is_string( $value ) ) {
            return trim( $value ) !== '';
        }

        return ! is_null( $value );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s field is required.', 'wpmvc' ), ':attribute' );
    }
}
