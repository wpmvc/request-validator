<?php
/**
 * Different rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Different
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Different extends Rule {
    protected $other_field;

    public function __construct( $other_field ) {
        $this->other_field = $other_field;
    }

    public static function get_name(): string {
        return 'different';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $other_value = $this->validator->wp_rest_request->get_param( $this->other_field );

        return $value !== $other_value;
    }

    public function replace_placeholders( string $message ): string {
        $other_name = $this->validator ? ( $this->validator->custom_attributes[$this->other_field] ?? $this->other_field ) : $this->other_field;
        return str_replace( ':other', $other_name, $message );
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: other field name */
        return sprintf( __( 'The %1$s and %2$s must be different.', 'wpmvc' ), ':attribute', ':other' );
    }

    public function get_other_field() {
        return $this->other_field;
    }
}
