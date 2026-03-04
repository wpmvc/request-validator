<?php

namespace WpMVC\RequestValidator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class ValidationEngineTest extends TestCase {
    public function test_deep_wildcard_expansion() {
        $request = new WP_REST_Request();
        $request->set_param(
            'payload', [
                'meta' => [
                    ['id' => 1, 'name' => 'John'],
                    ['id' => 2, 'name' => 'Jane']
                ]
            ]
        );

        // Validate that each ID must be an integer and exactly 1 digit
        $validation = new Validation(
            $request, [
                'payload.meta.*.id'   => 'required|integer|digits:1',
                'payload.meta.*.name' => 'required|alpha|max:10'
            ]
        );

        $this->assertTrue( $validation->passes(), 'Deep wildcard validation failed on valid payload.' );
        
        // Add an invalid payload deep in the structure
        $request->set_param(
            'payload', [
                'meta' => [
                    ['id' => '12', 'name' => 'John'], // Invalid: ID digits should be 1
                ]
            ]
        );
        
        $validation2 = new Validation(
            $request, [
                'payload.meta.*.id' => 'required|integer|digits:1',
            ]
        );
        
        $this->assertTrue( $validation2->fails(), 'Deep wildcard failed to reject invalid nested payload.' );
        $this->assertArrayHasKey( 'payload.meta.0.id', $validation2->errors() );
    }

    public function test_rule_type_evaluation_pipe_and_array_syntax() {
        $request = new WP_REST_Request();
        $request->set_param( 'title', 'ValidTitle' );

        // Mixed syntax
        $validation = new Validation(
            $request, [
                'title' => ['required', 'alpha', 'max:50'] // Array syntax
            ]
        );

        $this->assertTrue( $validation->passes(), 'Array syntax failed.' );

        $validation2 = new Validation(
            $request, [
                'title' => 'required|alpha|max:50' // Pipe syntax
            ]
        );

        $this->assertTrue( $validation2->passes(), 'Pipe syntax failed.' );
    }

    public function test_circuit_breaker_mechanics_bail_and_nullable() {
        $request = new WP_REST_Request();
        
        // --- nullable ---
        $validation_nullable = new Validation(
            $request, [
                'optional_field' => 'nullable|email' // optional_field is missing completely
            ]
        );
        
        $this->assertTrue( $validation_nullable->passes(), 'Nullable failed to circuit-break on missing field.' );

        $request->set_param( 'optional_field', '' ); // null/empty string
        $validation_nullable_empty = new Validation(
            $request, [
                'optional_field' => 'nullable|email'
            ]
        );
        $this->assertTrue( $validation_nullable_empty->passes(), 'Nullable failed to circuit-break on empty string.' );

        $request->set_param( 'optional_field', 'invalid-email' );
        $validation_nullable_invalid = new Validation(
            $request, [
                'optional_field' => 'nullable|email'
            ]
        );
        $this->assertTrue( $validation_nullable_invalid->fails(), 'Nullable allowed invalid formatted data.' );

        // --- bail ---
        $request->set_param( 'title', 'invalid-data' ); // Not an array, not min size
        $validation_bail = new Validation(
            $request, [
                'title' => 'required|array|min:5'
            ]
        );
        
        $this->assertTrue( $validation_bail->fails() );
        // Since it's not array, the array validation failed. Wait, bail is for stopping on FIRST failure.
        $this->assertCount( 2, $validation_bail->errors()['title'], 'Without bail, multiple errors should be registered.' );

        $validation_with_bail = new Validation(
            $request, [
                'title' => 'bail|required|array|min:5'
            ]
        );
        
        $this->assertCount( 1, $validation_with_bail->errors()['title'], 'Bail failed to stop validation after first failure.' );
    }

    public function test_internal_method_fallbacks_like_date_validator() {
        $request = new WP_REST_Request();
        $request->set_param( 'start_date', '2023-01-01' );
        
        // Instead of resolving a class, this falls back to $this->date_validator internally from DateTime trait.
        $validation = new Validation(
            $request, [
                'start_date' => 'required|date:Y-m-d'
            ]
        );

        $this->assertTrue( $validation->passes(), 'Fallback to trait validator failed on valid data.' );

        $request->set_param( 'start_date', '01/01/2023' ); // Invalid format
        $validation_invalid = new Validation(
            $request, [
                'start_date' => 'required|date:Y-m-d'
            ]
        );

        $this->assertTrue( $validation_invalid->fails(), 'Fallback to trait validator failed to reject invalid data.' );
    }
}
