<?php
/**
 * Abstract Rule class.
 *
 * @package WpMVC\RequestValidator\Rules
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator\Rules;

defined( "ABSPATH" ) || exit;

use WpMVC\RequestValidator\Contracts\Rule as RuleContract;
use WpMVC\RequestValidator\Validation;

/**
 * Class Rule
 *
 * Base class for all validation rules.
 *
 * @package WpMVC\RequestValidator\Rules
 */
abstract class Rule implements RuleContract {
    /**
     * The fluently-set custom validation error message.
     *
     * @var string|null
     */
    protected $custom_message;

    /**
     * The validator instance.
     *
     * @var Validation|null
     */
    protected $validator;

    /**
     * Set the validator instance.
     *
     * @param  \WpMVC\RequestValidator\Validation  $validator
     * @return $this
     */
    public function set_validator( $validator ) {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Set a custom validation error message.
     *
     * @param  string  $message
     * @return $this
     */
    public function message( $message ) {
        $this->custom_message = $message;
        return $this;
    }

    /**
     * Replace placeholders in the given message.
     *
     * @param  string  $message
     * @return string
     */
    public function replace_placeholders( string $message ): string {
        return $message;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function get_message(): string {
        return (string) ( $this->custom_message ?? $this->default_message() );
    }

    /**
     * Get the fluently-set custom validation error message.
     *
     * @return string|null
     */
    public function get_custom_message(): ?string {
        return $this->custom_message;
    }

    /**
     * Get the default validation error message.
     *
     * @return string
     */
    protected function default_message(): string {
        return '';
    }

    /**
     * Get the type of the attribute being validated.
     *
     * @return string
     */
    public function get_attribute_type(): string {
        if ( ! $this->validator ) {
            return 'string';
        }

        $rules = $this->validator->explode_rules ?? [];

        if ( in_array( 'numeric', $rules, true ) || in_array( 'integer', $rules, true ) ) {
            return 'numeric';
        }

        if ( in_array( 'array', $rules, true ) ) {
            return 'array';
        }

        if ( in_array( 'file', $rules, true ) ) {
            return 'file';
        }

        return 'string';
    }
}
