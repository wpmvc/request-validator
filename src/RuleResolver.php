<?php
/**
 * RuleResolver class.
 *
 * @package WpMVC\RequestValidator
 * @author  WpMVC
 * @license MIT
 */

namespace WpMVC\RequestValidator;

defined( "ABSPATH" ) || exit;

/**
 * Class RuleResolver
 *
 * Responsible for mapping and resolving rule strings to their respective class instances.
 *
 * @package WpMVC\RequestValidator
 */
class RuleResolver {
    /**
     * Map of rule names to their class names.
     * 
     * @var array
     */
    protected static array $rules = [
        'accepted'          => Rules\Accepted::class,
        'required'          => Rules\Required::class,
        'required_if'       => Rules\RequiredIf::class,
        'prohibited_unless' => Rules\ProhibitedUnless::class,
        'date'              => Rules\Date::class,
        'date_equals'       => Rules\DateEquals::class,
        'before'            => Rules\Before::class,
        'before_or_equal'   => Rules\BeforeOrEqual::class,
        'after'             => Rules\After::class,
        'after_or_equal'    => Rules\AfterOrEqual::class,
        'confirmed'         => Rules\Confirmed::class,
        'bail'              => Rules\Bail::class,
        'file'              => Rules\File::class,
        'mimes'             => Rules\Mimes::class,
        'mimetypes'         => Rules\Mimetypes::class,
        'image'             => Rules\Image::class,
        'min'               => Rules\Min::class,
        'max'               => Rules\Max::class,
        'between'           => Rules\Between::class,
        'same'              => Rules\Same::class,
        'different'         => Rules\Different::class,
        'size'              => Rules\Size::class,
        'digits'            => Rules\Digits::class,
        'digits_between'    => Rules\DigitsBetween::class,
        'in'                => Rules\In::class,
        'not_in'            => Rules\NotIn::class,
        'email'             => Rules\Email::class,
        'url'               => Rules\Url::class,
        'uuid'              => Rules\Uuid::class,
        'regex'             => Rules\Regex::class,
        'not_regex'         => Rules\NotRegex::class,
        'alpha'             => Rules\Alpha::class,
        'alpha_dash'        => Rules\AlphaDash::class,
        'alpha_num'         => Rules\AlphaNum::class,
        'mac_address'       => Rules\MacAddress::class,
        'numeric'           => Rules\Numeric::class,
        'integer'           => Rules\Integer::class,
        'boolean'           => Rules\Boolean::class,
        'json'              => Rules\Json::class,
        'timezone'          => Rules\Timezone::class,
        'ip'                => Rules\Ip::class,
        'ipv4'              => Rules\Ipv4::class,
        'ipv6'              => Rules\Ipv6::class,
        'starts_with'       => Rules\StartsWith::class,
        'ends_with'         => Rules\EndsWith::class,
        'array'             => Rules\ArrayRule::class,
        'string'            => Rules\StringRule::class,
    ];

    /**
     * Cached instances of stateless rules.
     *
     * @var array
     */
    protected static array $cache = [];

    /**
     * Resolve a rule string to a Rule instance.
     * 
     * @param string $rule
     * @param array $parameters
     * @return \WpMVC\RequestValidator\Contracts\Rule|null
     */
    public static function resolve( string $rule, array $parameters = [] ) {
        if ( ! isset( self::$rules[$rule] ) ) {
            return null;
        }

        $class = self::$rules[$rule];

        // Some rules might need special instantiation or multiple parameters
        switch ( $rule ) {
            case 'required_if':
            case 'between':
            case 'digits_between':
            case 'date_equals':
            case 'before':
            case 'before_or_equal':
            case 'after':
            case 'after_or_equal':
                return new $class( $parameters[0], $parameters[1] ?? 'Y-m-d' );
            case 'date':
                return new $class( $parameters[0] ?? 'Y-m-d' );
            case 'prohibited_unless':
                return new $class( $parameters[0], array_slice( $parameters, 1 ) );
            case 'mimes':
            case 'mimetypes':
            case 'in':
            case 'not_in':
            case 'starts_with':
            case 'ends_with':
                return new $class( $parameters );
            case 'min':
            case 'max':
            case 'size':
            case 'digits':
            case 'regex':
            case 'not_regex':
                return new $class( $parameters[0] );
            default:
                if ( empty( $parameters ) ) {
                    if ( ! isset( self::$cache[$rule] ) ) {
                        self::$cache[$rule] = new $class();
                    }
                    return self::$cache[$rule];
                }
                return new $class( ...$parameters );
        }
    }
}
