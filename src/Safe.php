<?php

declare(strict_types=1);

namespace KetPHP\Utils;

use Throwable;

/**
 * Class Safe
 *
 * Provides safe access to potentially unsafe values.
 * Prevents exceptions when accessing undefined variables or executing callables.
 *
 * Example:
 * ```
 * $data = ['key' => 'value'];
 *
 * $result = Safe::get($data['key'], 'default', fn($v) => trim($v), Safe::CAST_STRING); // value
 * $result2 = Safe::get($data['key2'], 'default', fn($v) => trim($v), Safe::CAST_STRING); // default
 * ```
 *
 * @package KetPHP\Utils
 */
final class Safe
{

    /** @var string Cast to integer */
    public const CAST_INT = 'int';

    /** @var string Cast to float */
    public const CAST_FLOAT = 'float';

    /** @var string Cast to string */
    public const CAST_STRING = 'string';

    /** @var string Cast to boolean */
    public const CAST_BOOL = 'bool';

    /** @var string Cast to array */
    public const CAST_ARRAY = 'array';

    /** @var string Cast to object */
    public const CAST_OBJECT = 'object';

    /**
     * Safely returns a value with optional fallback, transformation and casting.
     *
     * - Executes $value if it is callable.
     * - If $value is null or throws an exception, returns $default.
     * - Executes $callback only if the result came from $value (not from $default).
     * - Optionally casts the final value to a specific type.
     *
     * @param mixed $value    The primary value or callable to retrieve.
     * @param mixed $default  The default fallback value or callable if $value is null or fails.
     * @param callable|null $transform A transformation callback applied only if $value was successful.
     * @param string|null $cast Optional type casting (Safe::CAST_* constants).
     *
     * @return mixed The safe, processed and optionally cast value.
     */
    public static function get(
        mixed $value,
        mixed $default = null,
        ?callable $transform = null,
        ?string $cast = null
    ): mixed {
        $result = null;
        $usedDefault = false;

        try {
            $result = is_callable($value) ? $value() : $value;
        } catch (Throwable) {
            $usedDefault = true;
        }

        if ($result === null || $usedDefault) {
            try {
                $result = is_callable($default) ? $default() : $default;
            } catch (Throwable) {
                $result = null;
            }
            $usedDefault = true;
        }

        if ($usedDefault === false && $transform !== null) {
            try {
                $result = $transform($result);
            } catch (Throwable) {}
        }

        if ($cast !== null) {
            $result = self::cast($result, $cast);
        }

        return $result;
    }

    /**
     * Safely casts a value to the given type.
     *
     * @param mixed $value The value to cast.
     * @param string $type One of the Safe::CAST_* constants.
     *
     * @return mixed The casted value or the original one if type is unknown.
     */
    private static function cast(mixed $value, string $type): mixed
    {
        try {
            return match ($type) {
                self::CAST_INT => (int) $value,
                self::CAST_FLOAT => (float) $value,
                self::CAST_STRING => (string) $value,
                self::CAST_BOOL => (bool) $value,
                self::CAST_ARRAY => (array) $value,
                self::CAST_OBJECT => (object) $value,
                default => $value,
            };
        } catch (Throwable) {
            return $value;
        }
    }
}