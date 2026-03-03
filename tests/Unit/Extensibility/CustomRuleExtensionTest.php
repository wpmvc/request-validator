<?php

namespace WpMVC\RequestValidator\Tests\Unit\Extensibility;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WpMVC\RequestValidator\Contracts\Rule;
use WP_REST_Request;

class UppercaseRule implements Rule {
    public static function get_name(): string {
        return 'uppercase';
    }

    public function passes( string $attribute, $value ): bool {
        return strtoupper( $value ) === $value;
    }

    public function get_message(): string {
        return 'The field must be uppercase.';
    }

    public function replace_placeholders( string $message ): string {
        return $message;
    }
}

class CustomRuleExtensionTest extends TestCase {
    public function test_it_accepts_custom_rule_instances() {
        $request = new WP_REST_Request();
        $request->set_param( 'code', 'LOWERCASE' ); // wait, UPPERCASE is valid, lowercase is invalid
        $request->set_param( 'code_valid', 'UPPERCASE' );

        $validation = new Validation(
            $request, [
                'code'       => [new UppercaseRule()],
                'code_valid' => ['required', new UppercaseRule()]
            ]
        );

        $this->assertTrue( $validation->passes(), 'Validation should pass with custom rules evaluating to true.' );
    }

    public function test_validation_with_proper_case() {
        $request = new WP_REST_Request();
        $request->set_param( 'code', 'lowercase' ); // Invalid
        $request->set_param( 'code_valid', 'UPPERCASE' ); // Valid

        $validation = new Validation(
            $request, [
                'code'       => [new UppercaseRule()],
                'code_valid' => ['required', new UppercaseRule()]
            ]
        );

        $this->assertTrue( $validation->fails() );
        $this->assertArrayHasKey( 'code', $validation->errors() );
        $this->assertArrayNotHasKey( 'code_valid', $validation->errors() );
        $this->assertEquals( 'The field must be uppercase.', $validation->errors()['code'][0] );
    }
}
