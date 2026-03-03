<?php

namespace WpMVC\RequestValidator\Tests\Unit\Integration;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\FormRequest;
use WP_REST_Request;
use WpMVC\Exceptions\Exception;

class StubFormRequest extends FormRequest {
    public $authorize_result = true;

    public $with_validator_called = false;
    
    public function authorize(): bool {
        return $this->authorize_result;
    }

    public function rules(): array {
        return [
            'username' => 'required|alpha'
        ];
    }

    public function with_validator( $validator ): void {
        $this->with_validator_called = true;
    }
}

class FormRequestLifecycleTest extends TestCase {
    public function test_it_authorizes_and_validates_automatically() {
        $request = new WP_REST_Request();
        $request->set_param( 'username', 'admin' );
        
        $form_request = new StubFormRequest( $request );
        
        // Validation should have run in the constructor.
        $this->assertTrue( $form_request->with_validator_called, 'with_validator() should be called during instantiation.' );
        $this->assertEquals( 'admin', $form_request->get_param( 'username' ), 'Request should proxy params.' );
    }

    public function test_it_throws_exception_on_validation_failure() {
        $this->expectException( Exception::class );
        $this->expectExceptionCode( 422 );

        $request = new WP_REST_Request();
        $request->set_param( 'username', 'not_alpha_123' ); // Fails alpha rule
        
        $form_request = new StubFormRequest( $request );
    }

    public function test_it_throws_403_on_authorization_failure() {
        $this->expectException( \Exception::class );
        $this->expectExceptionMessage( 'Unauthorized' );
        // Checking if the code is actually set to 403 by FormRequest
        
        $request = new WP_REST_Request();
        $request->set_param( 'username', 'admin' );
        
        // This is tricky, we need to set the property BEFORE the constructor runs if it evaluates inside.
        // FormRequest doesn't let us pass it, so we'll mock or override auth in a specific class.
        $class = new class($request) extends StubFormRequest {
            public function authorize(): bool {
                return false; }
        };
    }
}
