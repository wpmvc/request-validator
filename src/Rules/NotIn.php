<?php
/**
 * NotIn rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class NotIn
 *
 * @package WpMVC\RequestValidator\Rules
 */
class NotIn extends Rule {
    protected $values;

    public function __construct( array $values ) {
        $this->values = $values;
    }

    public static function get_name(): string {
        return 'not_in';
    }

    public function passes( string $attribute, $value ): bool {
        return ! in_array( (string) $value, $this->values, true );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The selected %1$s is invalid.', 'wpmvc' ), ':attribute' );
    }
}
