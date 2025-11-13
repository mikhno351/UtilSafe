# KetSafe

![GitHub Release](https://img.shields.io/github/v/release/mikhno351/UtilSafe)
![GitHub Downloads (all assets, all releases)](https://img.shields.io/github/downloads/mikhno351/UtilSafe/total?logo=github&logoColor=white)
![Packagist Downloads](https://img.shields.io/packagist/dt/ket-php/utils-safe?logo=packagist&logoColor=white)
![Static Badge](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)



## Installation
Install via Composer:
```
composer require ket-php/utils-safe
```

## Usage
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
$value = Safe::get('   John   ', null, 'trim');
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

## Constants for Casting
| Constant            | Description     |
| ------------------- | --------------- |
| `Safe::CAST_INT`    | Cast to integer |
| `Safe::CAST_FLOAT`  | Cast to float   |
| `Safe::CAST_STRING` | Cast to string  |
| `Safe::CAST_BOOL`   | Cast to boolean |
| `Safe::CAST_ARRAY`  | Cast to array   |
| `Safe::CAST_OBJECT` | Cast to object  |
