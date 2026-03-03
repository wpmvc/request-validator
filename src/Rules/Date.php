<?php
/**
 * Date rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

use WpMVC\RequestValidator\DateTime;

/**
 * Class Date
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Date extends Rule {
    use DateTime;

    public function __construct( $format = 'Y-m-d' ) {
        $this->format = $format;
    }

    public static function get_name(): string {
        return 'date';
    }

    public function passes( string $attribute, $value ): bool {
        return ! empty( $value ) && $this->is_it_valid_date( $value, $this->format );
    }

    protected function default_message(): string {
        /* translators: %s: attribute name */
        return sprintf( __( 'The %1$s is not a valid date.', 'wpmvc' ), ':attribute' );
    }
}
