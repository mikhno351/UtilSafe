<?php

declare(strict_types=1);

namespace KetPHP\Utils;

use KetPHP\Utils\Common\Cast;

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
 *  var_dump(Truth::of(1)); // true
 *  var_dump(Truth::of('on')); // true
 *  var_dump(Truth::of('off')); // false
 *  var_dump(Truth::of('another')); // false
 *  ```
 *
 * @package KetPHP\Utils
 */
final class Truth
{

    /**
     * Global list of truthy values for non-strict comparison.
     *
     * @var array<int, mixed>
     */
    private static array $globalTruthyValues = [1, true, '1', 'true', 'on', 'yes', 'y', '+'];

    /**
     * Default list of truthy values for non-strict comparison.
     *
     * @var array<int, mixed>
     */
    private static array $defaultTruthyValues = [1, true, '1', 'true', 'on', 'yes', 'y', '+'];

    /**
     * Updates the global list of truthy values.
     *
     * You can pass any types — not just strings.
     *
     * @param array<int, mixed>|null $truthy Custom list of truthy values.
     * @param bool $merge Whether to merge with existing truthy values instead of replacing them.
     *
     * @return void
     */
    public static function configure(?array $truthy = null, bool $merge = false): void
    {
        if (is_array($truthy) === true && count($truthy) >= 1) {
            $filteredValues = array_values(array_filter($truthy, static fn($value) => is_null($value) === false));

            if ($merge === true && empty(self::$globalTruthyValues) === false) {
                self::$globalTruthyValues = array_values(array_unique(array_merge(self::$globalTruthyValues, $filteredValues)));
            } else {
                self::$globalTruthyValues = $filteredValues;
            }
        }
    }

    /**
     * Safely convert a value to boolean.
     *
     * @param mixed $value The value to convert.
     * @param bool $strict Only accept true/1/'true'/'1' as true.
     * @param array<int, mixed>|null $customTruthies Optional custom truthy list.
     *
     * @return bool
     */
    public static function of(mixed $value, bool $strict = false, ?array $customTruthies = null): bool
    {
        return Safe::get($value, false, fn($value) => self::convert($value, $strict, $customTruthies) === true, Cast::BOOLEAN);
    }

    /**
     * Internal conversion logic.
     *
     * @param mixed $value
     * @param bool $strict
     * @param array<int, mixed>|null $customTruthies
     *
     * @return bool
     */
    private static function convert(mixed $value, bool $strict, ?array $customTruthies): bool
    {
        if ($strict === true) {
            return $value === true || $value === 1 || $value === '1' || $value === 'true';
        }
        if (is_bool($value) === true) {
            return $value;
        }
        if (is_int($value) === true || is_float($value) === true) {
            return $value != 0;
        }
        if ($value === null) {
            return false;
        }

        $list = is_array($customTruthies) === true ? $customTruthies : self::$globalTruthyValues;

        if (is_string($value) === true) {
            $normalized = mb_strtolower(trim($value));
            foreach ($list as $truthy) {
                if (is_string($truthy) === true && mb_strtolower($truthy) === $normalized) {
                    return true;
                }
                if ($truthy === $value) {
                    return true;
                }
            }
        } else {
            return in_array($value, $list, true) === true;
        }

        return false;
    }

    public static function useDefaultConfigure(): void
    {
        self::$globalTruthyValues = self::$defaultTruthyValues;
    }
}