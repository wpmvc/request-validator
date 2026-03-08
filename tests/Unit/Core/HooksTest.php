<?php

namespace WpMVC\RequestValidator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class HooksTest extends TestCase {
    public function test_after_hooks_are_executed() {
        $request = new WP_REST_Request();
        $request->set_param( 'username', 'admin' );

        $validation = new Validation(
            $request, [
                'username' => 'required|alpha'
            ]
        );

        $hook_executed = false;

        $validation->after(
            function ( $validator ) use ( &$hook_executed ) {
                $hook_executed = true;
                $this->assertInstanceOf( Validation::class, $validator );
            }
        );

        // Trigger validation. after() hooks execute after the main validation loop.
        $this->assertTrue( $validation->passes(), 'passes() should return true for a valid payload.' );
        $this->assertTrue( $hook_executed, 'The after() hook was not executed.' );
    }
}
