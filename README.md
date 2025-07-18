<p align="center">
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/dt/wpmvc/request-validator" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/v/wpmvc/request-validator" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/wpmvc/request-validator"><img src="https://img.shields.io/packagist/l/wpmvc/request-validator" alt="License"></a>
</p>

# Request Validator

The `Request Validator` provides a robust and extensible system for validating REST API requests in WordPress plugins. It supports a fluent rule-based syntax inspired by Laravel and integrates seamlessly with `WP_REST_Request`.

---

### 📦 Installation

If using standalone:

```bash
composer require wpmvc/validator
```

---

### 🧱 Structure Overview

| File        | Purpose                                        |
| ----------- | ---------------------------------------------- |
| `Validator` | Main validation handler class                  |
| `Mime`      | Utility for validating uploaded file types     |
| `DateTime`  | Trait for handling date-based validation rules |

---

### 🚀 Basic Usage

Inside a controller method:

```php
public function store( Validator $validator, WP_REST_Request $request ) {
    $validator->validate([
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

### 🛠 Available Rules

| Rule              | Description                                          |
| ----------------- | ---------------------------------------------------- |
| `required`        | Field must be present and non-empty                  |
| `string`          | Must be a string                                     |
| `email`           | Validates email format                               |
| `numeric`         | Must be a numeric value                              |
| `integer`         | Must be an integer                                   |
| `boolean`         | Must be `true` or `false`                            |
| `array`           | Must be an array                                     |
| `uuid`            | Must be a valid UUID                                 |
| `url`             | Must be a valid URL                                  |
| `mac_address`     | Must be a valid MAC address                          |
| `json`            | Must be a valid JSON string                          |
| `confirmed`       | `field_confirmation` must match field                |
| `accepted`        | Value must be in allowed list                        |
| `file`            | Checks file upload validity                          |
| `mimes`           | Allowed file extensions (e.g. `jpg,png`)             |
| `max`             | Max length/size/value (string, numeric, file, array) |
| `min`             | Min length/size/value (string, numeric, file, array) |
| `date:format`     | Must be a date in given format                       |
| `date_equals`     | Must exactly match a date                            |
| `before`          | Must be before given date                            |
| `after`           | Must be after given date                             |
| `before_or_equal` | Must be before or equal to given date                |
| `after_or_equal`  | Must be after or equal to given date                 |

---

### 📁 File Validation Example

```php
$validator->validate([
    'photo' => 'required|file|mimes:png,jpg|max:1024',
]);
```

* Validates that `photo` is a file
* Accepts only `png`, `jpg`
* Maximum 1MB (1024 KB)

---

### 📅 Date Validation Example

```php
$validator->validate([
    'launched' => 'required|date:Y-m-d|after_or_equal:2022-01-01',
]);
```

Supports custom formats and comparison logic using native PHP `DateTime`.

---

### 📋 Error Handling

If `throw_errors` is `true` (default), the validator throws an exception and returns a 422 JSON response:

```json
{
  "message": "",
  "errors": {
    "email": ["The email field must be a valid email address."],
    "price": ["The price must be at least 0."]
  }
}
```

---

### 🔄 Manual Check

You can also use:

```php
$validator->validate($rules, false);
if ( $validator->is_fail() ) {
    return Response::send(['errors' => $validator->errors], 422);
}
```

---

### 🔧 Internal Utilities

* `Mime` class for file type validation using `mime_content_type`.
* `DateTime` trait for parsing and comparing dates based on format.
* `get_format()`, `is_it_valid_date()`, `get_timestamp()` handle date logic.
