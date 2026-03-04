<?php

namespace WpMVC\RequestValidator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class NestedValidationTest extends TestCase {
    /**
     * Test deeply nested validation (10 levels).
     */
    public function test_deeply_nested_validation() {
        $request = new WP_REST_Request( 'POST', '/test' );
        
        // Construct 10 level nested array
        $data = [
            'l1' => [
                'l2' => [
                    'l3' => [
                        'l4' => [
                            'l5' => [
                                'l6' => [
                                    'l7' => [
                                        'l8' => [
                                            'l9' => [
                                                'l10' => 'invalid-email'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $request->set_body_params( $data );

        $rules = [
            'l1.l2.l3.l4.l5.l6.l7.l8.l9.l10' => 'required|email',
        ];

        $validation = new Validation( $request, $rules );
        
        $this->assertTrue( $validation->fails() );
        $errors = $validation->errors();
        
        $this->assertArrayHasKey( 'l1.l2.l3.l4.l5.l6.l7.l8.l9.l10', $errors );
        $this->assertContains( 'The l1.l2.l3.l4.l5.l6.l7.l8.l9.l10 must be a valid email address.', $errors['l1.l2.l3.l4.l5.l6.l7.l8.l9.l10'] );
    }

    /**
     * Test wildcard validation at multiple levels.
     */
    public function test_wildcard_nested_validation() {
        $request = new WP_REST_Request( 'POST', '/test' );
        
        $data = [
            'users' => [
                [
                    'posts' => [
                        ['id' => 1, 'meta' => ['key' => '']],
                        ['id' => 2, 'meta' => ['key' => 'val']],
                    ]
                ],
                [
                    'posts' => [
                        ['id' => 3, 'meta' => ['key' => '']],
                    ]
                ]
            ]
        ];
        
        $request->set_body_params( $data );

        $rules = [
            'users.*.posts.*.meta.key' => 'required',
        ];

        $validation = new Validation( $request, $rules );
        $this->assertTrue( $validation->fails() );
        $errors = $validation->errors();
        
        // Should have errors for index 0.0 and 1.0
        $this->assertArrayHasKey( 'users.0.posts.0.meta.key', $errors );
        $this->assertArrayHasKey( 'users.1.posts.0.meta.key', $errors );
        $this->assertArrayNotHasKey( 'users.0.posts.1.meta.key', $errors );
    }

    /**
     * Test the "same" rule with nested dot notation.
     */
    public function test_same_rule_with_nested_dot_notation() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_body_params( [
            'user' => [
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ],
        ] );

        $rules = [
            'user.password_confirmation' => 'same:user.password',
        ];

        $validation = new Validation( $request, $rules );
        
        $this->assertTrue( $validation->passes(), 'The "same" rule should support dot notation for nested fields.' );
    }
}
