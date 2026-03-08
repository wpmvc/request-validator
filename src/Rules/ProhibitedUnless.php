<?php
/**
 * ProhibitedUnless rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class ProhibitedUnless
 *
 * @package WpMVC\RequestValidator\Rules
 */
class ProhibitedUnless extends Rule {
    protected $other_field;

    protected $values;

    public function __construct( $other_field, array $values ) {
        $this->other_field = $other_field;
        $this->values      = $values;
    }

    public static function get_name(): string {
        return 'prohibited_unless';
    }

    public function passes( string $attribute, $value ): bool {
        if ( ! $this->validator ) {
            return true;
        }

        $target_value = $this->validator->get_value( $this->other_field );

        // If the other field's value is in our allowed values, then the field is allowed
        if ( in_array( (string) $target_value, array_map( 'strval', $this->values ), true ) ) {
            return true;
        }

        // Otherwise, it is prohibited (must not be present)
        return ! $this->validator->data_has( $attribute );
    }

    protected function default_message(): string {
        /* translators: 1: attribute name, 2: other field name, 3: list of values */
        return sprintf( __( 'The %1$s field is prohibited unless %2$s is in %3$s.', 'wpmvc' ), ':attribute', ':other', ':values' );
    }

    public function replace_placeholders( string $message ): string {
        $other_name = $this->validator ? ( $this->validator->custom_attributes[$this->other_field] ?? $this->other_field ) : $this->other_field;
        return str_replace( [':other', ':values'], [$other_name, implode( ', ', $this->values )], $message );
    }

    public function get_other_field() {
        return $this->other_field;
    }

    public function get_values() {
        return $this->values;
    }
}
