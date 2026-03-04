<?php

namespace WpMVC\RequestValidator\Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;

class PrimitiveTypeRuleTest extends TestCase {
    /**
     * @dataProvider typeDataProvider
     */
    public function test_primitive_types( $rule_name, $value, $expected ) {
        $rule = RuleResolver::resolve( $rule_name );
        $this->assertEquals( $expected, $rule->passes( 'test', $value ) );
    }

    public function typeDataProvider() {
        return [
            ['numeric', '123', true],
            ['numeric', 123.45, true],
            ['numeric', 'abc', false],
            
            ['integer', '123', true],
            ['integer', 123, true],
            ['integer', 123.45, false],
            
            ['boolean', true, true],
            ['boolean', false, true],
            ['boolean', 1, true],
            ['boolean', 0, true],
            ['boolean', '1', true],
            ['boolean', '0', true],
            ['boolean', 'true', true],
            ['boolean', 'false', true],
            ['boolean', 'on', false], // Unlike larval we're strictly checking boolean filter wait - let's see. PHP filter checks on?
            
            ['array', [], true],
            ['array', ['a'], true],
            ['array', 'string', false],
            
            ['json', '{"key":"value"}', true],
            ['json', '[1, 2, 3]', true],
            ['json', 'not json', false],
            ['json', '{"key":}', false],
        ];
    }
}
