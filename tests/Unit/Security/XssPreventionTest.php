<?php

namespace WpMVC\RequestValidator\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class XssPreventionTest extends TestCase {
    public function test_it_rejects_xss_payloads_on_alpha_rules() {
        $request = new WP_REST_Request();
        
        // A standard XSS payload attempting to break out
        $request->set_param( 'payload', '<script>alert("xss")</script>' );

        $validation = new Validation(
            $request, [
                'payload' => 'alpha'
            ]
        );

        $this->assertTrue( $validation->fails(), 'Alpha rule incorrectly allowed HTML/JS syntax.' );
        $this->assertArrayHasKey( 'payload', $validation->errors() );
    }

    public function test_it_rejects_xss_in_emails() {
        $request = new WP_REST_Request();
        
        $request->set_param( 'email', '"><script>alert(1)</script>@example.com' );

        $validation = new Validation(
            $request, [
                'email' => 'email'
            ]
        );

        $this->assertTrue( $validation->fails(), 'Email rule incorrectly allowed XSS payload.' );
    }
}
