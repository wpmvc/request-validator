<?php
/**
 * RequiredIf rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class RequiredIf
 *
 * @package WpMVC\RequestValidator\Rules
 */
class RequiredIf extends Rule {
    protected $other_field;

    protected $value;

    public function __construct( $other_field, $value ) {
        $this->other_field = $other_field;
        $this->value       = $value;
    }

    public static function get_name(): string {
        return 'required_if';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $target_value = $this->validator->get_value( $this->other_field );

        if ( (string) $target_value === (string) $this->value ) {
            return ! $this->validator->data_is_empty( $attribute );
        }

        return true; 
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: other field name, 3: value */
        return sprintf( __( 'The %1$s field is required when %2$s is %3$s.', 'wpmvc' ), ':attribute', ':other', ':value' );
    }

    public function replace_placeholders( string $message ): string {
        $other_name = $this->validator ? ( $this->validator->custom_attributes[$this->other_field] ?? $this->other_field ) : $this->other_field;
        return str_replace( [':other', ':value'], [$other_name, $this->value], $message );
    }

    public function get_other_field() {
        return $this->other_field;
    }

    public function get_value() {
        return $this->value;
    }
}
