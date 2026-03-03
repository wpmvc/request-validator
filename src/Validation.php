<?php
/**
 * Validation class.
 *
 * @package WpMVC\RequestValidator
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

use WpMVC\Exceptions\Exception;
use WP_REST_Request;

/**
 * Class Validation
 *
 * Core validation engine for handling request and array validation.
 *
 * @package WpMVC\RequestValidator
 */
class Validation {
    /**
     * The WordPress REST request instance.
     *
     * @var WP_REST_Request
     */
    public WP_REST_Request $wp_rest_request;

    use HasMime;

    /**
     * The validation rules to be applied.
     *
     * @var array
     */
    protected array $rules;

    /**
     * Custom validation messages.
     *
     * @var array
     */
    protected array $messages;

    /**
     * Custom attribute names for validation.
     *
     * @var array
     */
    public array $custom_attributes;

    /**
     * The exploded rules for the current attribute.
     *
     * @var array
     */
    public array $explode_rules;

    /**
     * The validation errors.
     *
     * @var array
     */
    public array $errors = [];

    /**
     * The registered "after" validation callbacks.
     *
     * @var array
     */
    protected array $after_callbacks = [];

    /**
     * Indicates if the after-hooks have already run.
     *
     * @var bool
     */
    protected bool $hooks_ran = false;

    /**
     * Flattened keys for wildcard validation.
     *
     * @var array
     */
    protected array $flattened_keys = [];

    /**
     * Cache for exploded rule strings.
     *
     * @var array
     */
    protected array $rule_cache = [];

    /**
     * Cache for dot-separated segments.
     *
     * @var array
     */
    protected array $segment_cache = [];

    use DateTime;

    /**
     * Validation constructor.
     *
     * @param WP_REST_Request $wp_rest_request
     * @param array           $rules
     * @param array           $messages
     * @param array           $custom_attributes
     */
    public function __construct( WP_REST_Request $wp_rest_request, array $rules, array $messages = [], array $custom_attributes = [] ) {
        $this->wp_rest_request   = $wp_rest_request;
        $this->rules             = $rules;
        $this->messages          = $messages;
        $this->custom_attributes = $custom_attributes;
        
        $this->validate();
    }

    /**
     * Run the validation rules.
     *
     * @return void
     */
    protected function validate() {
        foreach ( $this->rules as $input_name => $rule ) {
            $this->validate_attribute( $input_name, $rule );
        }
    }

    /**
     * Validate a given attribute against a set of rules.
     *
     * @param  string  $attribute
     * @param  mixed   $rules
     * @return void
     */
    protected function validate_attribute( string $attribute, $rules ) {
        if ( strpos( $attribute, '*' ) !== false ) {
            $this->validate_wildcard_attribute( $attribute, $rules );
            return;
        }

        $rules_key = is_string( $rules ) ? $rules : null;
        if ( $rules_key && ! isset( $this->rule_cache[$rules_key] ) ) {
            $this->rule_cache[$rules_key] = explode( '|', $rules );
        }
        $explode_rules = $rules_key ? $this->rule_cache[$rules_key] : ( is_array( $rules ) ? $rules : [ $rules ] );
        
        // Handle 'sometimes' rule: skip if attribute is missing
        if ( in_array( 'sometimes', $explode_rules, true ) && ! $this->data_has( $attribute ) ) {
            return;
        }

        $this->explode_rules = $explode_rules;
        
        $value       = $this->get_value( $attribute );
        $is_nullable = in_array( 'nullable', $explode_rules, true );
        $is_bail     = in_array( 'bail', $explode_rules, true ) || in_array( new Rules\Bail, $explode_rules, false );
        $is_empty    = $value === '' || $value === null;

        if ( $is_nullable && $is_empty ) {
            return;
        }

        foreach ( $explode_rules as $explode_rule ) {
            if ( $is_bail && ! empty( $this->errors[$attribute] ) ) {
                break;
            }

            if ( in_array( $explode_rule, [ 'nullable', 'sometimes', 'bail' ], true ) || $explode_rule instanceof Rules\Bail ) {
                continue;
            }
            
            if ( $explode_rule instanceof Contracts\Rule ) {
                $this->validate_custom_rule( $attribute, $explode_rule );
            } elseif ( $explode_rule instanceof \Closure ) {
                $this->validate_closure_rule( $attribute, $explode_rule );
            } else {
                $this->validate_rule( $attribute, $explode_rule );
            }
        }
    }

    /**
     * Validate a wildcard attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $rules
     * @return void
     */
    protected function validate_wildcard_attribute( string $attribute, $rules ) {
        if ( empty( $this->flattened_keys ) ) {
            $this->flattened_keys = $this->get_all_keys( $this->wp_rest_request->get_params() );
        }
        
        $pattern = str_replace( '\*', '[^.]+', preg_quote( $attribute, '/' ) );
        
        $matched_keys = preg_grep( '/^' . $pattern . '$/', $this->flattened_keys );

        foreach ( $matched_keys as $matched_key ) {
            $this->validate_attribute( $matched_key, $rules );
        }
    }

    /**
     * Get all keys from an array recursively.
     *
     * @param  array   $array
     * @param  string  $prefix
     * @param  array   $keys
     * @return array
     */
    protected function get_all_keys( array $array, $prefix = '', array &$keys = [] ) {
        foreach ( $array as $key => $value ) {
            $full_key = $prefix === '' ? $key : "{$prefix}.{$key}";
            $keys[]   = $full_key;
            if ( is_array( $value ) ) {
                $this->get_all_keys( $value, $full_key, $keys );
            }
        }
        return $keys;
    }

    /**
     * Get the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get_value( string $key ) {
        $data = $this->wp_rest_request->get_params();
        return $this->data_get( $data, $key );
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    protected function data_get( $target, $key, $default = null ) {
        if ( is_null( $key ) ) {
            return $target;
        }

        $segments = $this->segment_cache[$key] ?? ( $this->segment_cache[$key] = explode( '.', $key ) );

        foreach ( $segments as $segment ) {
            if ( is_array( $target ) && array_key_exists( $segment, $target ) ) {
                $target = $target[$segment];
            } else {
                return $default;
            }
        }

        return $target;
    }

    /**
     * Determine if the validation fails.
     *
     * @return bool
     */
    public function fails(): bool {
        $this->run_after_hooks();
        return ! empty( $this->errors );
    }

    /**
     * Determine if the validation passes.
     *
     * @return bool
     */
    public function passes(): bool {
        $this->run_after_hooks();
        return empty( $this->errors );
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function errors(): array {
        $this->run_after_hooks();
        return $this->errors;
    }

    /**
     * Throw an exception if the validation fails.
     *
     * @return void
     *
     * @throws Exception
     */
    public function throw_if_fails() {
        if ( $this->fails() ) {
            throw ( new Exception( '', 422 ) )->set_messages( $this->errors );
        }
    }

    /**
     * Register an "after" validation callback.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function after( callable $callback ): self {
        $this->after_callbacks[] = $callback;
        
        return $this;
    }

    /**
     * Run all of the "after" validation hooks.
     *
     * @return void
     */
    protected function run_after_hooks() {
        if ( $this->hooks_ran ) {
            return;
        }

        $this->hooks_ran = true;

        foreach ( $this->after_callbacks as $callback ) {
            call_user_func( $callback, $this );
        }
    }

    /**
     * Validate a rule by name.
     *
     * @param  string  $input_name
     * @param  string  $rule
     * @return void
     *
     * @throws Exception
     */
    protected function validate_rule( string $input_name, string $rule ) {
        $rule_explode = explode( ':', $rule, 2 );
        $rule_name    = $rule_explode[0];
        $parameters   = isset( $rule_explode[1] ) ? explode( ',', $rule_explode[1] ) : [];

        // Resolve via RuleResolver
        $rule_instance = RuleResolver::resolve( $rule_name, $parameters );

        if ( $rule_instance ) {
            $this->validate_custom_rule( $input_name, $rule_instance );
            return;
        }

        // Fallback for custom methods if they exist (backward compatibility or internal)
        $method = "{$rule_name}_validator";
        if ( method_exists( static::class, $method ) ) {
            $this->$method( $input_name, isset( $rule_explode[1] ) ? $rule_explode[1] : '' );
        } else {
            throw new Exception(
                sprintf(
                    /* translators: %s: rule name */
                    __( '%s rule not found' ), $rule_name
                )
            );
        }
    }

    /**
     * Validate an attribute using a custom rule object.
     *
     * @param  string          $input_name
     * @param  Contracts\Rule  $rule
     * @return void
     */
    protected function validate_custom_rule( string $input_name, Contracts\Rule $rule ) {
        // Make the rule validator-aware if it's one of our built-in Rule objects
        if ( $rule instanceof Rules\Rule ) {
            $rule->set_validator( $this );
        }

        if ( ! $this->data_has( $input_name ) ) {
            // By default, Custom rules shouldn't fail if empty. They require 'required' usually.
            // But we can check empty string to mirror Laravel.
            $value = null;
        } else {
            $value = $this->get_value( $input_name );
        }

        if ( ! $rule->passes( $input_name, $value ) ) {
            $rule_name = $rule::get_name();
            
            // ISSUE FIX: Hierarchical message lookup.
            // 1. Fluent message (set via ->message() on rule object)
            // 2. Attribute-specific custom message (e.g., 'email.required')
            // 3. Rule-generic custom message (e.g., 'required')
            // 4. Type-aware custom message (e.g., 'min.numeric')
            // 5. Default message (defined in the rule class)
            
            $message = null;

            // COMPATIBILITY: We check for 'get_custom_message' to support external custom rules 
            // that might not extend our base Rule class but still want to provide custom messages.
            if ( method_exists( $rule, 'get_custom_message' ) ) {
                $message = $rule->get_custom_message();
            }

            if ( empty( $message ) ) {
                $custom_key = "{$input_name}.{$rule_name}";
                if ( isset( $this->messages[$custom_key] ) ) {
                    $message = $this->messages[$custom_key];
                } elseif ( isset( $this->messages[$rule_name] ) ) {
                    $message = $this->messages[$rule_name];
                } elseif ( in_array( $rule_name, [ 'min', 'max', 'size', 'between' ], true ) ) {
                    $type = $rule->get_attribute_type();
                    if ( isset( $this->messages["{$rule_name}.{$type}"] ) ) {
                        $message = $this->messages["{$rule_name}.{$type}"];
                    }
                }
            }

            // Fallback to the rule's internal get_message() if no custom message found
            if ( empty( $message ) ) {
                $message = $rule->get_message();
            }
            
            $attribute_name              = $this->custom_attributes[$input_name] ?? $input_name;
            $message                     = (string) str_replace( ':attribute', $attribute_name, $message );
            $this->errors[$input_name][] = $message;

            // Always run the placeholder replacement logic for rule-specific placeholders (like :min, :max, :date)
            $this->apply_custom_rule_message( $input_name, $rule );
        }
    }

    /**
     * Apply the custom rule message and replace placeholders.
     *
     * @param  string          $input_name
     * @param  Contracts\Rule  $rule
     * @return void
     */
    protected function apply_custom_rule_message( string $input_name, Contracts\Rule $rule ) {
        if ( empty( $this->errors[$input_name] ) ) {
            return;
        }

        // We'll replace placeholders in the last added error message for this input
        $message = $this->errors[$input_name][ count( $this->errors[$input_name] ) - 1 ];

        // Use the rule's own placeholder replacement logic
        $message = $rule->replace_placeholders( $message );
        
        $this->errors[$input_name][ count( $this->errors[$input_name] ) - 1 ] = $message;
    }

    /**
     * Validate an attribute using a closure.
     *
     * @param  string    $input_name
     * @param  \Closure  $rule
     * @return void
     */
    protected function validate_closure_rule( string $input_name, \Closure $rule ) {
        if ( ! $this->data_has( $input_name ) ) {
            $value = null;
        } else {
            $value = $this->get_value( $input_name );
        }

        $fail = function ( $message ) use ( $input_name ) {
            $attribute_name              = $this->custom_attributes[$input_name] ?? $input_name;
            $message                     = (string) str_replace( ':attribute', $attribute_name, $message );
            $this->errors[$input_name][] = $message;
        };

        $rule( $input_name, $value, $fail );
    }

    /**
     * Determine if the data contains the given key.
     *
     * @param  string  $key
     * @return bool
     */
    public function data_has( string $key ): bool {
        $data = $this->wp_rest_request->get_params();
        
        $segments = $this->segment_cache[$key] ?? ( $this->segment_cache[$key] = explode( '.', $key ) );

        foreach ( $segments as $segment ) {
            if ( is_array( $data ) && array_key_exists( $segment, $data ) ) {
                $data = $data[$segment];
            } else {
                // Check files for top-level keys
                if ( count( $segments ) === 1 ) {
                    $files = $this->wp_rest_request->get_file_params();
                    return isset( $files[$key] );
                }
                return false;
            }
        }
        
        return true;
    }
}
