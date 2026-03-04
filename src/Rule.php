<?php

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

use WpMVC\RequestValidator\Rules\Between;
use WpMVC\RequestValidator\Rules\In;
use WpMVC\RequestValidator\Rules\NotIn;
use WpMVC\RequestValidator\Rules\Digits;
use WpMVC\RequestValidator\Rules\DigitsBetween;
use WpMVC\RequestValidator\Rules\Size;
use WpMVC\RequestValidator\Rules\RequiredIf;
use WpMVC\RequestValidator\Rules\ProhibitedUnless;
use WpMVC\RequestValidator\Rules\Same;
use WpMVC\RequestValidator\Rules\Different;
use WpMVC\RequestValidator\Rules\Image;
use WpMVC\RequestValidator\Rules\Mimetypes;
use WpMVC\RequestValidator\Rules\Min;
use WpMVC\RequestValidator\Rules\Max;
use WpMVC\RequestValidator\Rules\Required;
use WpMVC\RequestValidator\Rules\Bail;

class Rule {
    /**
     * Get a required constraint.
     *
     * @return \WpMVC\RequestValidator\Rules\Required
     */
    public static function required() {
        return new Required();
    }

    /**
     * Get a bail constraint.
     *
     * @return \WpMVC\RequestValidator\Rules\Bail
     */
    public static function bail() {
        return new Bail();
    }

    /**
     * Get a between constraint.
     *
     * @param  mixed  $min
     * @param  mixed  $max
     * @return \WpMVC\RequestValidator\Rules\Between
     */
    public static function between( $min, $max ) {
        return new Between( $min, $max );
    }

    /**
     * Get an in constraint.
     *
     * @param  array  $values
     * @return \WpMVC\RequestValidator\Rules\In
     */
    public static function in( array $values ) {
        return new In( $values );
    }

    /**
     * Get a not_in constraint.
     *
     * @param  array  $values
     * @return \WpMVC\RequestValidator\Rules\NotIn
     */
    public static function not_in( array $values ) {
        return new NotIn( $values );
    }

    /**
     * Get a digits constraint.
     *
     * @param  int  $digits
     * @return \WpMVC\RequestValidator\Rules\Digits
     */
    public static function digits( $digits ) {
        return new Digits( $digits );
    }

    /**
     * Get a digits_between constraint.
     *
     * @param  int  $min
     * @param  int  $max
     * @return \WpMVC\RequestValidator\Rules\DigitsBetween
     */
    public static function digits_between( $min, $max ) {
        return new DigitsBetween( $min, $max );
    }

    /**
     * Get a size constraint.
     *
     * @param  int  $size
     * @return \WpMVC\RequestValidator\Rules\Size
     */
    public static function size( $size ) {
        return new Size( $size );
    }

    /**
     * Get a required_if constraint.
     *
     * @param  string  $other_field
     * @param  mixed  $value
     * @return \WpMVC\RequestValidator\Rules\RequiredIf
     */
    public static function required_if( $other_field, $value ) {
        return new RequiredIf( $other_field, $value );
    }

    /**
     * Get a prohibited_unless constraint.
     *
     * @param  string  $other_field
     * @param  array  $values
     * @return \WpMVC\RequestValidator\Rules\ProhibitedUnless
     */
    public static function prohibited_unless( $other_field, array $values ) {
        return new ProhibitedUnless( $other_field, $values );
    }

    /**
     * Get a same constraint.
     *
     * @param  string  $other_field
     * @return \WpMVC\RequestValidator\Rules\Same
     */
    public static function same( $other_field ) {
        return new Same( $other_field );
    }

    /**
     * Get a different constraint.
     *
     * @param  string  $other_field
     * @return \WpMVC\RequestValidator\Rules\Different
     */
    public static function different( $other_field ) {
        return new Different( $other_field );
    }

    /**
     * Get an image constraint.
     *
     * @return \WpMVC\RequestValidator\Rules\Image
     */
    public static function image() {
        return new Image();
    }

    /**
     * Get a mimetypes constraint.
     *
     * @param  array  $allowed_mimetypes
     * @return \WpMVC\RequestValidator\Rules\Mimetypes
     */
    public static function mimetypes( array $allowed_mimetypes ) {
        return new Mimetypes( $allowed_mimetypes );
    }

    /**
     * Get a min constraint.
     *
     * @param  mixed  $min
     * @return \WpMVC\RequestValidator\Rules\Min
     */
    public static function min( $min ) {
        return new Min( $min );
    }

    /**
     * Get a max constraint.
     *
     * @param  mixed  $max
     * @return \WpMVC\RequestValidator\Rules\Max
     */
    public static function max( $max ) {
        return new Max( $max );
    }
}
