<?php

namespace WpMVC\RequestValidator\Tests\Unit\Rules\Format;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;

class NetworkFormatRuleTest extends TestCase {
    /**
     * @dataProvider ipDataProvider
     */
    public function test_ip_rules( $value, $is_ip, $is_ipv4, $is_ipv6 ) {
        $ip_rule = RuleResolver::resolve( 'ip' );
        $this->assertEquals( $is_ip, $ip_rule->passes( 'test', $value ) );

        $ipv4_rule = RuleResolver::resolve( 'ipv4' );
        $this->assertEquals( $is_ipv4, $ipv4_rule->passes( 'test', $value ) );

        $ipv6_rule = RuleResolver::resolve( 'ipv6' );
        $this->assertEquals( $is_ipv6, $ipv6_rule->passes( 'test', $value ) );
    }

    public function ipDataProvider() {
        return [
            ['192.168.1.1',     true, true,  false],
            ['10.0.0.0',        true, true,  false],
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334', true, false, true],
            ['::1',             true, false, true],
            ['not_an_ip',       false, false, false],
            ['256.256.256.256', false, false, false],
            ['1200::AB00:1234::2552:7777:1313', false, false, false] // Double ::
        ];
    }
}
