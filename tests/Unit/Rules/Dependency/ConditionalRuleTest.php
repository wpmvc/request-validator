<?php

namespace WpMVC\RequestValidator\Tests\Unit\Rules\Dependency;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class ConditionalRuleTest extends TestCase {
    public function test_required_if_rule() {
        $request = new WP_REST_Request();
        $request->set_param( 'type', 'admin' );
        
        // required_if:type,admin
        $validation = new Validation(
            $request, [
                'password' => 'required_if:type,admin'
            ]
        );

        $this->assertTrue( $validation->fails(), 'Required validation skipped when condition was met.' );

        // provide the field
        $request->set_param( 'password', 'secret' );
        $validation2 = new Validation(
            $request, [
                'password' => 'required_if:type,admin'
            ]
        );
        $this->assertTrue( $validation2->passes(), 'Required validation failed when field was provided.' );

        // change condition so it's not required
        $request->set_param( 'type', 'guest' );
        $request->set_param( 'password', '' );
        $validation3 = new Validation(
            $request, [
                'password' => 'required_if:type,admin'
            ]
        );
        $this->assertTrue( $validation3->passes(), 'Required validation ran even when condition was NOT met.' );
    }

    public function test_prohibited_unless_rule() {
        $request = new WP_REST_Request();
        $request->set_param( 'type', 'guest' );
        $request->set_param( 'access_code', '123' );
        
        // prohibited_unless:type,admin
        // means access_code is prohibited UNLESS type is admin
        // Since type is guest, it is prohibited!
        $validation = new Validation(
            $request, [
                'access_code' => 'prohibited_unless:type,admin'
            ]
        );

        $this->assertTrue( $validation->fails(), 'Prohibited rule allowed field when condition was NOT met.' );

        // Provide the correct admin type, access_code should now be allowed (ignored)
        $request->set_param( 'type', 'admin' );
        $validation2 = new Validation(
            $request, [
                'access_code' => 'prohibited_unless:type,admin'
            ]
        );
        $this->assertTrue( $validation2->passes(), 'Prohibited rule rejected field even though condition was met.' );
    }
}
