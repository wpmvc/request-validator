<?php

namespace WpMVC\RequestValidator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\Validation;
use WP_REST_Request;

class DateTimeFormatTest extends TestCase {
    /**
     * Test different date formats.
     */
    public function test_different_date_formats() {
        $formats = [
            'Y-m-d' => '2023-12-25',
            'd/m/Y' => '25/12/2023',
            'm-d-Y' => '12-25-2023',
            'Y.m.d' => '2023.12.25',
            'd-M-Y' => '25-Dec-2023',
        ];

        foreach ( $formats as $format => $value ) {
            $request = new WP_REST_Request( 'POST', '/test' );
            $request->set_param( 'date_field', $value );

            $rules = [
                'date_field' => "date:{$format}",
            ];

            $validation = new Validation( $request, $rules );
            $this->assertTrue( $validation->passes(), "Failed asserting that {$value} is a valid date for format {$format}" );
        }
    }

    /**
     * Test date time formats.
     */
    public function test_date_time_formats() {
        $formats = [
            'Y-m-d H:i:s'   => '2023-12-25 14:30:00',
            'Y-m-d H:i'     => '2023-12-25 14:30',
            'd/m/Y g:i A'   => '25/12/2023 2:30 PM',
            'Y-m-d\TH:i:s'  => '2023-12-25T14:30:00',
            'Y-m-d\TH:i:sP' => '2023-12-25T14:30:00+01:00',
            'H:i'           => '14:30',
        ];

        foreach ( $formats as $format => $value ) {
            $request = new WP_REST_Request( 'POST', '/test' );
            $request->set_param( 'datetime_field', $value );

            $rules = [
                'datetime_field' => "date:{$format}",
            ];

            $validation = new Validation( $request, $rules );
            $this->assertTrue( $validation->passes(), "Failed asserting that {$value} is a valid datetime for format {$format}" );
        }
    }

    /**
     * Test before and after rules with different formats.
     */
    public function test_before_and_after_with_custom_formats() {
        // Test with Y-m-d H:i
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'start_time', '2023-12-25 10:00' );
        $request->set_param( 'end_time', '2023-12-25 11:00' );

        $rules = [
            'start_time' => 'date:Y-m-d H:i',
            'end_time'   => 'date:Y-m-d H:i|after:start_time,Y-m-d H:i',
        ];

        $validation = new Validation( $request, $rules );
        $this->assertTrue( $validation->passes(), 'Failed after:start_time with Y-m-d H:i' );

        // Test with invalid order
        $request->set_param( 'end_time', '2023-12-25 09:00' );
        $validation = new Validation( $request, $rules );
        $this->assertTrue( $validation->fails(), 'Failed asserting that 09:00 is not after 10:00' );
    }

    /**
     * Test date_equals rule with custom format.
     */
    public function test_date_equals_with_custom_format() {
        $request = new WP_REST_Request( 'POST', '/test' );
        $request->set_param( 'date_1', '25/12/2023' );
        $request->set_param( 'date_2', '25/12/2023' );

        $rules = [
            'date_1' => 'date:d/m/Y',
            'date_2' => 'date_equals:date_1,d/m/Y',
        ];

        $validation = new Validation( $request, $rules );
        $this->assertTrue( $validation->passes() );
    }
}
