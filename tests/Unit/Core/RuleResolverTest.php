<?php

namespace WpMVC\RequestValidator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use WpMVC\RequestValidator\RuleResolver;
use ReflectionProperty;

class RuleResolverTest extends TestCase {
    protected function tearDown(): void {
        parent::tearDown();
        // Reset the singleton cache after each test to prevent pollution
        $reflection = new ReflectionProperty( RuleResolver::class, 'cache' );
        if ( \PHP_VERSION_ID < 80100 ) {
            $reflection->setAccessible( true );
        }
        $reflection->setValue( null, [] );
    }

    public function test_it_resolves_stateless_rules_from_cache(): void {
        // First resolution should instantiate and cache
        $email_rule1 = RuleResolver::resolve( 'email' );
        
        // Second resolution should return the exact same object from cache
        $email_rule2 = RuleResolver::resolve( 'email' );

        $this->assertSame( $email_rule1, $email_rule2, 'RuleResolver should cache and return the exact same stateless rule instance.' );
    }

    public function test_it_correctly_hydrates_rule_parameters(): void {
        // Resolve a rule with parameters (not cached)
        $min_rule = RuleResolver::resolve( 'min', ['5'] );
        
        $this->assertInstanceOf( \WpMVC\RequestValidator\Rules\Min::class, $min_rule );
        
        $reflection = new ReflectionProperty( $min_rule, 'min' );
        if ( \PHP_VERSION_ID < 80100 ) {
            $reflection->setAccessible( true );
        }
        $this->assertEquals( '5', $reflection->getValue( $min_rule ) );
    }

    public function test_it_returns_null_for_unregistered_rules(): void {
        $rules = RuleResolver::resolve( 'non_existent_rule_123' );
        $this->assertNull( $rules, 'Unregistered rules should return null.' );
    }

    public function test_mac_address_typo_is_fixed(): void {
        $mac_rule = RuleResolver::resolve( 'mac_address' );
        $this->assertInstanceOf( \WpMVC\RequestValidator\Rules\MacAddress::class, $mac_rule );
    }

    public function test_it_resolves_multiple_parameters_correctly(): void {
        $between_rule = RuleResolver::resolve( 'between', ['1', '10'] );
        
        $this->assertInstanceOf( \WpMVC\RequestValidator\Rules\Between::class, $between_rule );

        $reflection_min = new ReflectionProperty( $between_rule, 'min' );
        if ( \PHP_VERSION_ID < 80100 ) {
            $reflection_min->setAccessible( true );
        }
        $this->assertEquals( '1', $reflection_min->getValue( $between_rule ) );

        $reflection_max = new ReflectionProperty( $between_rule, 'max' );
        if ( \PHP_VERSION_ID < 80100 ) {
            $reflection_max->setAccessible( true );
        }
        $this->assertEquals( '10', $reflection_max->getValue( $between_rule ) );
    }
}
