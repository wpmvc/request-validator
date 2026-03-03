<?php

namespace WpMVC\RequestValidator\Tests\Unit\Rules\Format;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;

class StringFormatRuleTest extends TestCase {
    /**
     * @dataProvider emailDataProvider
     */
    public function test_email_rule( $value, $expected ) {
        $rule = current( RuleResolver::resolve( 'email' ) ); // Returns rule object wrapped? Wait, RuleResolver::resolve returns Rule directly!
        $rule = RuleResolver::resolve( 'email' );
        
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function emailDataProvider() {
        return [
            ['test@example.com', true],
            ['invalid-email', false],
            ['test@sub.domain.com', true],
            ['@missinguser.com', false]
        ];
    }

    /**
     * @dataProvider urlDataProvider
     */
    public function test_url_rule( $value, $expected ) {
        $rule = RuleResolver::resolve( 'url' );
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function urlDataProvider() {
        return [
            ['https://google.com', true],
            ['http://example.com/path?query=1', true],
            ['invalid-url', false],
            ['ftp://domain.com', true], // filter_var validates ftp
        ];
    }

    /**
     * @dataProvider macAddressDataProvider
     */
    public function test_mac_address_rule( $value, $expected ) {
        $rule = RuleResolver::resolve( 'mac_address' );
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function macAddressDataProvider() {
        return [
            ['00:1A:2B:3C:4D:5E', true],
            ['00-1A-2B-3C-4D-5E', true],
            ['invalid-mac', false],
            ['00:1A:2B:3C:4D:5Z', false] // Invalid hex
        ];
    }

    /**
     * @dataProvider alphaDataProvider
     */
    public function test_alpha_rule( $value, $expected ) {
        $rule = RuleResolver::resolve( 'alpha' );
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function alphaDataProvider() {
        return [
            ['OnlyLetters', true],
            ['Letters123', false],
            ['With Spaces', false],
            ['special@', false]
        ];
    }

    /**
     * @dataProvider alphaDashDataProvider
     */
    public function test_alpha_dash_rule( $value, $expected ) {
        $rule = RuleResolver::resolve( 'alpha_dash' );
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function alphaDashDataProvider() {
        return [
            ['Letters-And_Dash', true],
            ['letters123', true],
            ['With Spaces', false],
            ['special@', false]
        ];
    }

    public function test_regex_rule() {
        $rule = RuleResolver::resolve( 'regex', ['/^[A-Z]+$/'] );
        $this->assertTrue( $rule->passes( 'test', 'UPPERCASE' ) );
        $this->assertFalse( $rule->passes( 'test', 'lowercase' ) );
        
        $not_regex = RuleResolver::resolve( 'not_regex', ['/^[A-Z]+$/'] );
        $this->assertFalse( $not_regex->passes( 'test', 'UPPERCASE' ) );
        $this->assertTrue( $not_regex->passes( 'test', 'lowercase' ) );
    }
}
