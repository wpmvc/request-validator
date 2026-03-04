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

    /**
     * Test that min rule treats non-numeric values as strings for length validation.
     */
    public function test_min_rule_without_numeric_is_treated_as_string_length() {
        $request = new WP_REST_Request();
        $request->set_param( 'age', '123' );
        $validation = new Validation( $request, ['age' => 'min:5'] );
        $this->assertTrue( $validation->fails(), 'Min rule without numeric should check string length.' );
    }
}
