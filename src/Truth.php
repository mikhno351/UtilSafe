<?php

declare(strict_types=1);

namespace KetPHP\Utils;

use Throwable;

/**
 * Utility class for safely converting various values to boolean.
 *
 * Features:
 * - Strict and non-strict modes.
 * - Configurable truthy value list.
 * - Fully exception-safe (never throws).
 * - Handles all scalar and mixed types gracefully.
 *
 * Example:
 *  ```
 *  $data = ['key1' => 1, 'key2' => 'on', 'key3' => 'off'];
 *
 *  $result = Truth::of($data['key1']); // true
 *  $result = Truth::of($data['key2']); // true
 *  $result = Truth::of($data['key3']); // false
 *  ```
 *
 * @package KetPHP\Utils
 */
final class Truth
{

    /**
     * Default list of truthy values for non-strict comparison.
     *
     * @var array<int, mixed>
     */
    private static array $truthyValues = [
        1, true, '1', 'true', 'on', 'yes', 'yep', 'y', 'ok', '+', '==', '===', 'active', 'enable', 'enabled',
        'check', 'checked', 'selected', 'accept', 'accepted', 'agree', 'allow', 'allowed', 'valid', 'ready'
    ];

    /**
     * Updates the global list of truthy values.
     *
     * You can pass any types — not just strings.
     *
     * @param array<int, mixed>|null $truthy Custom list of truthy values.
     *
     * @return void
     */
    public static function configure(?array $truthy = null): void
    {
        if (is_array($truthy)) {
            self::$truthyValues = array_values(array_filter($truthy, static fn($v) => $v !== null));
        }
    }

    /**
     * Safely converts any value to boolean.
     *
     * - In strict mode:
     *     Only `true`, `'true'`, `1`, `'1'` are true.
     * - In non-strict mode:
     *     Compares against built-in or custom truthy values (of any type).
     *
     * @param mixed $value The value to convert.
     * @param bool $strict Enable strict mode (only true/1 accepted).
     * @param array<int, mixed>|null $customTruthies Optional custom truthy list for this call.
     *
     * @return bool Normalized boolean.
     */
    public static function of(mixed $value, bool $strict = false, ?array $customTruthies = null): bool
    {
        try {
            if ($strict) {
                return $value === true || $value === 1 || $value === '1' || $value === 'true';
            }
            if ($value === null) {
                return false;
            }
            if (is_bool($value) === true) {
                return $value;
            }

            if (is_int($value) === true || is_float($value) === true) {
                return $value != 0;
            }

            $list = $customTruthies ?? self::$truthyValues;
            foreach ($list as $truthy) {
                if (self::compare($value, $truthy) === true) {
                    return true;
                }
            }

            if (is_string($value)) {
                $normalized = strtolower(trim($value));
                foreach ($list as $truthy) {
                    if (is_string($truthy) === true && strtolower($truthy) === $normalized) {
                        return true;
                    }
                }
            }

            return false;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Internal helper: smart comparison for any types.
     *
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private static function compare(mixed $a, mixed $b): bool
    {
        if (is_object($a) === true && is_object($b) === true) {
            if ($a === $b) {
                return true;
            }
            if (get_class($a) !== get_class($b)) {
                return false;
            }

            return true;
        }
        if (is_object($a) === true && is_string($b) === true) {
            return strcasecmp($a::class, $b) === 0;
        }
        if (is_string($a) === true && is_object($b) === true) {
            return strcasecmp($a, $b::class) === 0;
        }
        if (gettype($a) === gettype($b)) {
            return $a === $b;
        }
        if (is_scalar($a) === true && is_scalar($b) === true) {
            return (string)$a === (string)$b;
        }

        return false;
    }
}