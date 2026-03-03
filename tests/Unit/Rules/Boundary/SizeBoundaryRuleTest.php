<?php

namespace WpMVC\RequestValidator\Tests\Unit\Rules\Boundary;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;
use WP_REST_Request;
use WpMVC\RequestValidator\Validation;

class SizeBoundaryRuleTest extends TestCase {
    public function test_min_rule() {
        // String length
        $request = new WP_REST_Request();
        $request->set_param( 'test', '12345' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'min:5'] ) )->passes() );

        $request->set_param( 'test', '1234' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'min:5'] ) )->fails() );

        // Numeric value
        $request->set_param( 'test', 10 );
        $this->assertTrue( ( new Validation( $request, ['test' => 'numeric|min:10'] ) )->passes() );

        $request->set_param( 'test', 5 );
        $this->assertTrue( ( new Validation( $request, ['test' => 'numeric|min:10'] ) )->fails() );
    }

    public function test_between_rule() {
        $request = new WP_REST_Request();
        
        $request->set_param( 'test', '123456' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'between:5,10'] ) )->passes() );

        $request->set_param( 'test', '1234' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'between:5,10'] ) )->fails() );

        $request->set_param( 'test', [1,2,3,4,5,6] );
        $this->assertTrue( ( new Validation( $request, ['test' => 'array|between:5,10'] ) )->passes() );
        
        $request->set_param( 'test', [1,2,3] );
        $this->assertTrue( ( new Validation( $request, ['test' => 'array|between:5,10'] ) )->fails() );
    }

    public function test_digits_rule() {
        $request = new WP_REST_Request();
        
        $request->set_param( 'test', '123' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'digits:3'] ) )->passes() );
        
        $request->set_param( 'test', '12' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'digits:3'] ) )->fails() );
    }

    public function test_digits_between_rule() {
        $request = new WP_REST_Request();
        
        $request->set_param( 'test', '123' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'digits_between:2,4'] ) )->passes() );
        
        $request->set_param( 'test', '1' );
        $this->assertTrue( ( new Validation( $request, ['test' => 'digits_between:2,4'] ) )->fails() );
    }
}
