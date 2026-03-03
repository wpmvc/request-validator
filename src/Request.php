<?php
/**
 * Request class.
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
 * Class Request
 *
 * Extends WP_REST_Request to provide validation capabilities.
 *
 * @package WpMVC\RequestValidator
 */
class Request extends WP_REST_Request {
    /**
     * The validation errors.
     *
     * @var array
     */
    public array $errors = [];

    /**
     * Request constructor.
     *
     * @param WP_REST_Request $request
     */
    public function __construct( WP_REST_Request $request ) {
        parent::__construct();

        // Transfer state from the passed request object to $this
        $this->set_method( $request->get_method() );
        $this->set_url_params( $request->get_url_params() );
        $this->set_query_params( $request->get_query_params() );
        $this->set_body_params( $request->get_body_params() );
        $this->set_file_params( $request->get_file_params() );
        $this->set_default_params( $request->get_default_params() );
        $this->set_headers( $request->get_headers() );
        $this->set_body( $request->get_body() );
        $this->set_route( $request->get_route() );
        $this->set_attributes( $request->get_attributes() );
    }

    /**
     * Backward-compatible validation method and primary entry point for simple arrays of rules.
     * Instantiates the new Validation engine under the hood.
     *
     * @param array $rules
     * @param bool $throw_errors
     * @return array
     * @throws Exception
     */
    public function validate( array $rules, bool $throw_errors = true ) {
        $validation = $this->make( $this, $rules );

        if ( $throw_errors ) {
            $validation->throw_if_fails();
        }

        // Keep backwards compatibility for $this->errors state
        $this->errors = $validation->errors();

        return $this->errors;
    }

    /**
     * Create a new Validation instance to evaluate.
     * Useful for fluent checking without throwing exceptions automatically.
     * 
     * @param WP_REST_Request $request
     * @param array $rules
     * @param array $messages
     * @param array $custom_attributes
     * @return Validation
     */
    public function make( WP_REST_Request $request, array $rules, array $messages = [], array $custom_attributes = [] ): Validation {
        return new Validation( $request, $rules, $messages, $custom_attributes );
    }
}
