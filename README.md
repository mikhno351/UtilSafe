# KetSafe

![Packagist Version](https://img.shields.io/packagist/v/ket-php/utils-safe)
![Packagist Downloads](https://img.shields.io/packagist/dt/ket-php/utils-safe?logo=packagist&logoColor=white)
![Static Badge](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)



## Installation
Install via Composer:
```
composer require ket-php/utils-safe
```

## Usage

### Safe:
```php
use KetPHP\Utils\Safe;

// Simple value
$value = Safe::get('Hello'); 
echo $value; // Hello

// Value with default
$value = Safe::get(null, 'Default'); 
echo $value; // Default

// Using a callable
$value = Safe::get(fn() => 123); 
echo $value; // 123

// Transform only if value exists
$value = Safe::get('  John  ', 'Unknown', fn($v) => trim($v));
echo $value; // John

// Transform string function
$value = Safe::get('  John  ', 'Unknown', 'trim');
echo $value; // John

// Default value ignores transform
$value = Safe::get(null, 'Fallback', fn($v) => strtoupper($v));
echo $value; // Fallback

// Optional casting
$value = Safe::get('123', null, null, Safe::CAST_INT); 
echo $value; // 123 (integer)

$data = ['known' => 'value'];

// Without null coalescing
// WARNING: PHP would normally trigger a Notice (Undefined index)
$value = Safe::get($data['unknown'], 'Default');
echo $value; // Default (PHP Notice / E_USER_WARNING is triggered)

// You can suppress the warning using the @ operator
$value = Safe::get(@$data['unknown'], 'Default');
echo $value; // Default (no warning)

// With null coalescing
// Safe and no warning
$value = Safe::get($data['unknown'] ?? null, 'Default');
echo $value; // Default
```

#### Constants for Casting:
| Constant            | Description     |
| ------------------- | --------------- |
| `Safe::CAST_INT`    | Cast to integer |
| `Safe::CAST_FLOAT`  | Cast to float   |
| `Safe::CAST_STRING` | Cast to string  |
| `Safe::CAST_BOOL`   | Cast to boolean |
| `Safe::CAST_ARRAY`  | Cast to array   |
| `Safe::CAST_OBJECT` | Cast to object  |

### Truth:
```php
use KetPHP\Utils\Truth;

// Non-strict mode (default)
var_dump(Truth::of(1)); // true
var_dump(Truth::of('on')); // true
var_dump(Truth::of('no')); // false
var_dump(Truth::of(null)); // false

// Strict mode
//
// In strict mode, any truthy list is ignored (both global and per-call custom lists). The ONLY values considered true are: 1, '1', true, 'true'
var_dump(Truth::of('true', true)); // true
var_dump(Truth::of('on', true)); // false
var_dump(Truth::of(1, true)); // true
var_dump(Truth::of(0, true)); // false

// Using a callable
var_dump(Truth::of(fn() => 'yes')); // true
var_dump(Truth::of(fn() => 'no')); // false

// Custom truthy list for a single call
$custom = ['foo', 'bar', 123];
var_dump(Truth::of('foo', false, $custom)); // true
var_dump(Truth::of('baz', false, $custom)); // false

// Configure global truthy values
Truth::configure(['sure', 'ok']);
var_dump(Truth::of('ok')); // true
var_dump(Truth::of('yes')); // false (old default removed)
```
