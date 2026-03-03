<?php

namespace WpMVC\RequestValidator\Tests\Unit\Localization;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class MessageFormattingTest extends TestCase {
    public function test_decentralized_default_messages() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'email_field', 'invalid-email' );
        $request->set_param( 'min_field', 'abc' ); // string length 3, min 5
        
        $rules = [
            'email_field'    => 'email',
            'min_field'      => 'min:5',
            'required_field' => 'required',
        ];

        $validation = new Validation( $request, $rules );
        $errors     = $validation->errors();

        $this->assertContains( 'The email_field must be a valid email address.', $errors['email_field'] );
        $this->assertContains( 'The min_field must be at least 5 characters.', $errors['min_field'] );
        $this->assertContains( 'The required_field field is required.', $errors['required_field'] );
    }

    public function test_placeholder_replacement_in_complex_rules() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'digits_field', '123' ); // 3 digits, need 5
        $request->set_param( 'between_field', 10 ); // numeric between 1, 5
        
        $rules = [
            'digits_field'  => 'digits:5',
            'between_field' => 'numeric|between:1,5',
        ];

        $validation = new Validation( $request, $rules );
        $errors     = $validation->errors();

        $this->assertContains( 'The digits_field must be 5 digits.', $errors['digits_field'] );
        $this->assertContains( 'The between_field must be between 1 and 5.', $errors['between_field'] );
    }

    public function test_custom_attribute_names_in_messages() {
        $request = new WP_REST_Request( 'POST', '/test' );
        
        $rules             = [
            'first_name' => 'required',
        ];
        $custom_attributes = [
            'first_name' => 'First Name',
        ];

        $validation = new Validation( $request, $rules, [], $custom_attributes );
        $errors     = $validation->errors();

        $this->assertContains( 'The First Name field is required.', $errors['first_name'] );
    }

    public function test_between_rule_with_types() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'string_between', 'abc' ); // 3 chars, need 5-10
        $request->set_param( 'numeric_between', 15 ); // need 1-5
        
        $rules = [
            'string_between'  => 'between:5,10',
            'numeric_between' => 'numeric|between:1,5',
        ];

        $validation = new Validation( $request, $rules );
        $errors     = $validation->errors();

        $this->assertContains( 'The string_between must be between 5 and 10 characters.', $errors['string_between'] );
        $this->assertContains( 'The numeric_between must be between 1 and 5.', $errors['numeric_between'] );
    }

    public function test_date_rules_formatting() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'date_field', 'invalid' );
        $request->set_param( 'before_field', '2023-01-01' ); // need before 2022-01-01
        
        $rules = [
            'date_field'   => 'date',
            'before_field' => 'before:2022-01-01',
        ];

        $validation = new Validation( $request, $rules );
        $errors     = $validation->errors();

        $this->assertContains( 'The date_field is not a valid date.', $errors['date_field'] );
        $this->assertContains( 'The before_field must be a date before 2022-01-01.', $errors['before_field'] );
    }

    public function test_accepted_and_confirmed_rules() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'terms', 'no' );
        $request->set_param( 'password', 'secret' );
        $request->set_param( 'password_confirmation', 'wrong' );
        
        $rules = [
            'terms'    => 'accepted',
            'password' => 'confirmed',
        ];

        $validation = new Validation( $request, $rules );
        $errors     = $validation->errors();

        $this->assertContains( 'The terms must be accepted.', $errors['terms'] );
        $this->assertContains( 'The password confirmation does not match.', $errors['password'] );
    }

    public function test_custom_message_overrides() {
        $request = new WP_REST_Request( 'POST', '/test' );
        
        $rules = [
            'email_field'    => 'email',
            'numeric_field'  => 'numeric|min:10',
            'required_field' => 'required',
        ];

        $messages = [
            'email_field.email' => 'CUSTOM EMAIL MESSAGE',
            'required'          => 'GENERIC REQUIRED MESSAGE',
            'min.numeric'       => 'CUSTOM NUMERIC MIN MESSAGE',
        ];

        $validation = new Validation( $request, $rules, $messages );
        $errors     = $validation->errors();

        $this->assertContains( 'CUSTOM EMAIL MESSAGE', $errors['email_field'] );
        $this->assertContains( 'CUSTOM NUMERIC MIN MESSAGE', $errors['numeric_field'] );
        $this->assertContains( 'GENERIC REQUIRED MESSAGE', $errors['required_field'] );
    }

    public function test_fluent_message_override_priority() {
        $request = new WP_REST_Request( 'POST', '/test' );
        
        $rules = [
            'email_field' => ( new \WpMVC\RequestValidator\Rules\Email() )->message( 'FLUENT EMAIL MESSAGE' ),
        ];

        $messages = [
            'email_field.email' => 'ARRAY EMAIL MESSAGE',
        ];

        $validation = new Validation( $request, $rules, $messages );
        $errors     = $validation->errors();

        $this->assertContains( 'FLUENT EMAIL MESSAGE', $errors['email_field'] );
    }
}
