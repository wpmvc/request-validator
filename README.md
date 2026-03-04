<p align="center">
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/dt/wpmvc/request-validator" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/v/wpmvc/request-validator" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/l/wpmvc/request-validator" alt="License"></a>
</p>

# Request Validator

The `Request Validator` provides a robust and extensible system for validating REST API requests in WordPress plugins. It supports a fluent rule-based syntax inspired by Laravel and integrates seamlessly with `WP_REST_Request`.

---

### 📦 Installation

This package is included by default within the **WpMVC** framework. 

However, if you want to use this validation engine independently inside your own custom WordPress plugins, you may install it as a standalone package via Composer:

```bash
composer require wpmvc/request-validator
```
---

### 🚀 Basic Usage

Inside a controller method:

```php
use WpMVC\RequestValidator\Request;

public function store( Request $request ) {
    $request->validate([
        'title'    => 'required|string|min:3|max:255',
        'email'    => 'required|email',
        'price'    => 'numeric|min:0',
        'tags'     => 'array',
        'image'    => 'file|mimes:png,jpg,jpeg|max:2048',
        'launched' => 'date:Y-m-d|after_or_equal:2024-01-01',
    ]);

    // Validation passed; continue logic
}
```

> ❗ If validation fails, a `WpMVC\Exceptions\Exception` is thrown with HTTP status `422` and the error messages.

---

### 🏗 Form Requests

For more complex validation scenarios, you may wish to create a "form request" class. Form requests are custom request classes that encapsulate their own validation and authorization logic. 

To create one, extend the `FormRequest` class:

```php
namespace MyPlugin\App\Http\Requests;

use WpMVC\RequestValidator\FormRequest;

class StorePostRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'required',
            'status'  => 'required|in:publish,draft,pending'
        ];
    }
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array {
        return [
            'title.required' => 'A title is absolutely required for your post.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array {
        return [
            'status' => 'publication status',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function with_validator( $validator ): void {
        $validator->after(function ($validator) {
            if ( $this->something_else_is_invalid() ) {
                $validator->errors['field_name'][] = 'Something went wrong!';
            }
        });
    }
}
```

Once defined, you can seamlessly type-hint the class on your controller method. The incoming request is validated and authorized before the controller method is even called:

```php
public function store( StorePostRequest $request ) {
    // The incoming request is valid and authorized...
}
```

---

### 🛠 Available Rules

| Rule                          | Description                                                              |
| ----------------------------- | ------------------------------------------------------------------------ |
| `accepted`                    | Must be `yes`, `on`, `1`, `true`, `1`, or `true`.                        |
| `after:date`                  | Must be after the given date (supports relative dates like `today`).      |
| `after_or_equal:date`         | Must be after or equal to the given date.                                |
| `alpha`                       | Must be entirely alphabetic characters.                                  |
| `alpha_dash`                  | Must have alpha-numeric characters, dashes, and underscores.             |
| `alpha_num`                   | Must be entirely alpha-numeric characters.                               |
| `array`                       | Must be an array.                                                        |
| `bail`                        | Stop running validation rules after the first failure.                   |
| `before:date`                 | Must be before the given date.                                           |
| `before_or_equal:date`        | Must be before or equal to the given date.                               |
| `between:min,max`             | Must have a size between the given *min* and *max*.                      |
| `boolean`                     | Must be a boolean value.                                                 |
| `confirmed`                   | `field_confirmation` must match the field value.                         |
| `date:format`                 | Must be a date in the given format (default: `Y-m-d`).                   |
| `date_equals:date`            | Must exactly match the given date.                                       |
| `different:field`             | Must have a different value than the specified *field*.                  |
| `digits:value`                | Must be numeric and have an exact length of *value*.                     |
| `digits_between:min,max`      | Must be numeric and have a length between *min* and *max*.               |
| `email`                       | Must be a valid email address.                                           |
| `ends_with:foo,bar,...`       | Must end with one of the given values.                                   |
| `file`                        | Must be a valid uploaded file.                                           |
| `image`                       | Must be an image (jpeg, png, bmp, gif, svg, webp).                       |
| `in:foo,bar,...`              | Must be included in the given list of values.                            |
| `integer`                     | Must be an integer.                                                      |
| `ip`                          | Must be a valid IP address.                                              |
| `ipv4`                        | Must be a valid IPv4 address.                                            |
| `ipv6`                        | Must be a valid IPv6 address.                                            |
| `json`                        | Must be a valid JSON string.                                             |
| `mac_address`                 | Must be a valid MAC address.                                             |
| `max:value`                   | Max length/size/value (string, numeric, file, array).                    |
| `mimes:jpg,png,...`           | Allowed file extensions based on MIME types.                             |
| `mimetypes:text/plain,...`    | Must match one of the given MIME types.                                  |
| `min:value`                   | Min length/size/value (string, numeric, file, array).                    |
| `not_in:foo,bar,...`          | Must not be included in the given list of values.                        |
| `not_regex:pattern`           | Must not match the given regular expression.                             |
| `nullable`                    | Field may be null or empty.                                              |
| `numeric`                     | Must be a numeric value.                                                 |
| `prohibited_unless:field,val` | Prohibited unless *field* is equal to any of the values.                 |
| `regex:pattern`               | Must match the given regular expression.                                 |
| `required`                    | Field must be present and non-empty.                                     |
| `required_if:field,val`       | Required if the *field* is equal to the given *value*.                   |
| `same:field`                  | Must match the value of the given *field*.                               |
| `size:value`                  | Must have a matching size (string length, array count, file KB).         |
| `sometimes`                   | Run rules only if the field is present in the request.                   |
| `starts_with:foo,bar,...`     | Must start with one of the given values.                                 |
| `string`                      | Must be a string.                                                        |
| `timezone`                    | Must be a valid timezone identifier.                                     |
| `url`                         | Must be a valid URL.                                                     |
| `uuid`                        | Must be a valid UUID.                                                    |

---

### 📁 File Validation Example

```php
$request->validate([
    'photo' => 'required|file|mimes:png,jpg|max:1024',
]);
```

* Validates that `photo` is a file
* Accepts only `png`, `jpg`
* Maximum 1MB (1024 KB)

---

### 📅 Date Validation Example

```php
$request->validate([
    'launched' => 'required|date:Y-m-d|after_or_equal:2022-01-01',
]);
```

Supports custom formats and comparison logic using native PHP `DateTime`.

---

### 🧩 Array Validation Example

The validator supports wildcard dot-notation (`.*`) to validate elements within an array where the exact indexes are unknown. 

For instance, you can validate each email in an array of participants:

```php
// Request Payload: { "participants": [ { "email": "a@x.com" }, { "email": "b@y.com" } ] }

$request->validate([
    'participants'         => 'required|array',
    'participants.*.email' => 'required|email' // Evaluates participants.0.email, participants.1.email, etc.
]);
```

---

### 💬 Custom Error Messages & Rules

You can customize the error messages by passing a third array to the `$request->make($request, $rules, $messages, $attributes)` method, or by overriding the `messages()` and `attributes()` methods in your `FormRequest` class. 

#### Placeholders
The following placeholders can be used within your custom error messages:
- `:attribute`: The name of the field being validated.
- `:min`, `:max`, `:size`: Respective numeric/size values.
- `:other`: Name of the field being compared against.
- `:value`, `:values`: The value(s) required by the rule.
- `:format`: The expected date format.

Example:
```php
$messages = [
    'email.required' => 'We really need your :attribute!',
    'age.min'        => 'You must be at least :min years old.',
];
```

You can also pass inline Closures directly into the rules array for fast custom logic:

```php
$request->validate([
    'title' => [
        'required',
        function ($attribute, $value, $fail) {
            if ($value === 'reserved_word') {
                $fail("The {$attribute} contains an invalid word.");
            }
        },
    ]
]);
```

#### Custom Rule Objects

For complex validation, you can create a dedicated rule class by extending the `WpMVC\RequestValidator\Rules\Rule` base class:

```php
use WpMVC\RequestValidator\Rules\Rule;

class UppercaseRule extends Rule {
    public static function get_name(): string {
      return 'uppercase';
    }

    public function passes(string $attribute, $value): bool {
      return strtoupper($value) === $value;
    }

    protected function default_message(): string {
      /* translators: 1: attribute name */
      return sprintf( __( 'The %1$s must be completely uppercase.', 'wpmvc' ), ':attribute' );
    }
}

// In your controller:
$request->validate([
    'title' => ['required', new UppercaseRule()]
]);
```

---

### �🧱 Fluent Rule Builder

Instead of using pipe-separated strings, you may fluently construct validation rules using the `Rule` class. This is particularly useful for complex rules or when you need to avoid string concatenation errors (such as using an `in` rule with an array of predefined data).

```php
use WpMVC\RequestValidator\Rule;

$request->validate([
    'email'  => [Rule::required(), Rule::email(), Rule::max(255)],
    'status' => [Rule::required(), Rule::in(['active', 'pending', 'banned'])],
    'type'   => [Rule::required_if('is_admin', true)],
]);
```

---

### 📋 Validation Error Response Format

If validation fails, the engine throws a `Exception` that the WpMVC framework catches and automatically converts into a `422 Unprocessable Entity` JSON response. 

The JSON response format is structured as follows, where `errors` contains arrays of error messages keyed by the failing field name:

```json
{
  "data": {
    "status_code": 422
  },
  "messages": {
    "email": ["The email field must be a valid email address."],
    "price": ["The price must be at least 0."]
  }
}
```

---

### 🔄 Manual Check

You can also use:

```php
$request->validate($rules, false);
if ( $request->fails() ) {
    wp_send_json_error(['errors' => $request->errors], 422);
}
```

---

### 🪝 Validation Hooks (After)

The `Validation` engine allows you to attach callbacks to be run **after** validation completes. This allows you to easily perform further validation steps across multiple fields and assign custom error messages before failing.

```php
$validation = $request->make($request, $rules)->after(function ($validation) {
    if ( $this->something_else_is_invalid() ) {
        $validation->errors['field_name'][] = 'Something went wrong!';
    }
});

if ( $validation->fails() ) {
    // ...
}
```

---

### 🌍 Usage Outside of a Controller

If you want to use the validator in a different context (like a cron job, WP-CLI command, or a regular WordPress hook action), you can manually build a `WP_REST_Request` and pass it to either `Request` or `Validation`.

#### Using `Validation` directly:
```php
use WpMVC\RequestValidator\Validation;

$request = new \WP_REST_Request();
$request->set_param('email', 'test@example.com');

$validation = new Validation($request, [
    'email' => 'required|email'
]);

if ($validation->fails()) {
    $errors = $validation->errors();
}
```

#### Using `Request` class:
```php
use WpMVC\RequestValidator\Request;

$wp_request = new \WP_REST_Request();
$wp_request->set_params($_POST); // Hydrate with raw global data

$request = new Request($wp_request);
$request->validate([
    'title' => 'required|string|max:255'
]);
```