<?php
/**
 * Rule contract.
 *
 * @package WpMVC\RequestValidator\Contracts
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Contracts;

defined( "ABSPATH" ) || exit;

/**
 * Interface Rule
 *
 * @package WpMVC\RequestValidator\Contracts
 */
interface Rule {
    /**
     * Get the unique name of the rule.
     *
     * @return string
     */
    public static function get_name(): string;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes( string $attribute, $value ): bool;

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function get_message(): string;

    /**
     * Replace placeholders in the given message.
     *
     * @param  string  $message
     * @return string
     */
    public function replace_placeholders( string $message ): string;
}
