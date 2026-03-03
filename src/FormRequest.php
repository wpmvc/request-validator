<?php
/**
 * FormRequest abstract class.
 *
 * @package WpMVC\RequestValidator
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

use Exception;
use WP_REST_Request;

/**
 * Class FormRequest
 *
 * Base class for all form request validation classes.
 *
 * @package WpMVC\RequestValidator
 */
abstract class FormRequest extends Request {
    /**
     * @var Validation
     */
    protected Validation $validation;

    /**
     * FormRequest constructor.
     * Automatically triggers validation upon instantiation.
     * 
     * @param WP_REST_Request $request
     */
    public function __construct( WP_REST_Request $request ) {
        parent::__construct( $request );

        $this->validate();
    }

    /**
     * Determine if the user is authorized to make this request.
     * Default to true; can be overridden in child classes.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array {
        return [];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validation $validator
     * @return void
     */
    public function with_validator( Validation $validator ): void {
        // Can be overridden in child classes
    }

    /**
     * Validate the class rules against the incoming request.
     *
     * @throws Exception
     */
    public function validate( array $rules = [], bool $throw_errors = true ): void {
        if ( ! $this->authorize() ) {
            throw new Exception( __( 'Unauthorized' ), 403 );
        }

        $this->validation = $this->make(
            $this,
            ! empty( $rules ) ? $rules : $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        $this->with_validator( $this->validation );

        $this->validation->throw_if_fails();
    }
}
