<?php
/**
 * Confirmed rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Confirmed
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Confirmed extends Rule {
    public static function get_name(): string {
        return 'confirmed';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $confirmation_field = "{$attribute}_confirmation";
        $confirmation_value = $this->validator->wp_rest_request->get_param( $confirmation_field );

        return $value === $confirmation_value;
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s confirmation does not match.', 'wpmvc' ), ':attribute' );
    }
}
