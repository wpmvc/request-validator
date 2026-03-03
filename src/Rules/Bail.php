<?php
/**
 * Bail rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

/**
 * Class Bail
 *
 * @package WpMVC\RequestValidator\Rules
 */
class Bail extends Rule {
    public static function get_name(): string {
        return 'bail';
    }

    public function passes( string $attribute, $value ): bool {
        return true;
    }

    public function get_message(): string {
        return '';
    }
}
