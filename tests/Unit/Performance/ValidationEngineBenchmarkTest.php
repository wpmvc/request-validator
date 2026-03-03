<?php

namespace WpMVC\RequestValidator\Tests\Unit\Performance;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class ValidationEngineBenchmarkTest extends TestCase {
    public function test_memory_does_not_leak_significantly() {
        $request = new WP_REST_Request();
        $request->set_param( 'field1', 'john@example.com' );
        $request->set_param( 'field2', '12345' );
        
        $rules = [
            'field1' => 'required|email|max:50',
            'field2' => 'required|numeric|digits_between:1,10'
        ];

        // Warm up and record baseline
        $v = new Validation( $request, $rules );
        $v->passes();
        
        $start_memory = memory_get_usage();

        for ( $i = 0; $i < 1000; $i++ ) {
            $val = new Validation( $request, $rules );
            $val->passes();
        }

        $end_memory = memory_get_usage();
        $diff       = $end_memory - $start_memory;

        // Ensure memory usage doesn't grow by more than 2MB for 1000 iterations
        // The singleton cache in RuleResolver guarantees objects aren't redundantly recreated
        $this->assertLessThan(
            2 * 1024 * 1024, 
            $diff, 
            'Validation engine has a significant memory leak. Leak size: ' . $diff . ' bytes.'
        );
    }
}
