<?php

declare(strict_types=1);

namespace KetPHP\Utils;

use KetPHP\Utils\Common\Cast;
use Throwable;

/**
 * Class Safe
 *
 * Provides safe access to potentially unsafe values.
 * Prevents exceptions when accessing undefined variables or executing callables.
 *
 * Example:
 * ```
 * $data = ['known' => 'value'];
 *
 * echo Safe::get(@$data['known'], 'default', fn($v) => trim($v), Cast::STRING); // value
 * echo Safe::get(@$data['unknown'], 'default', 'trim', Cast::STRING); // default
 * ```
 *
 * @package KetPHP\Utils
 */
final class Safe
{

    /**
     * @deprecated
     *
     * @var string Cast to integer
     */
    public const CAST_INT = 'int';

    /**
     * @deprecated
     *
     * @var string Cast to float
     */
    public const CAST_FLOAT = 'float';

    /**
     * @deprecated
     *
     * @var string Cast to string
     */
    public const CAST_STRING = 'string';

    /**
     * @deprecated
     *
     * @var string Cast to boolean
     */
    public const CAST_BOOL = 'bool';

    /**
     * @deprecated
     *
     * @var string Cast to array
     */
    public const CAST_ARRAY = 'array';

    /**
     * @deprecated
     *
     * @var string Cast to object
     */
    public const CAST_OBJECT = 'object';

    /**
     * Safely returns a value with optional fallback, transformation and casting.
     *
     * - Executes $value if it is callable.
     * - If $value is null or throws an exception, returns $default.
     * - Executes $callback only if the result came from $value (not from $default).
     * - Optionally casts the final value to a specific type.
     *
     * @param mixed $value The primary value or callable to retrieve.
     * @param mixed $default The default fallback value or callable if $value is null or fails.
     * @param callable|null $transform A transformation callback applied only if $value was successful.
     * @param Cast|null|string $cast Optional type casting (Cast::* constants).
     *
     * @return mixed The safe, processed and optionally cast value.
     */
    public static function get(mixed $value, mixed $default = null, ?callable $transform = null, Cast|null|string $cast = null): mixed
    {
        $result = null;
        $usedDefault = false;

        try {
            $result = is_callable($value) === true ? $value() : $value;
        } catch (Throwable) {
            $usedDefault = true;
        }

        if (is_null($result) === true || $usedDefault) {
            try {
                $result = is_callable($default) === true ? $default() : $default;
            } catch (Throwable) {
                $result = null;
            }
            $usedDefault = true;
        }

        if ($usedDefault === false && is_callable($transform) === true) {
            try {
                $result = $transform($result);
            } catch (Throwable) {
            }
        }

        if ($cast instanceof Cast) {
            $cast = $cast->value;
        }

        if (is_null($cast) === false) {
            $result = self::cast($result, $cast);
        }

        return $result;
    }

    /**
     * Safely casts a value to the given type.
     *
     * @param mixed $value The value to cast.
     * @param string $type One of the Cast::* constants.
     *
     * @return mixed The casted value or the original one if type is unknown.
     */
    private static function cast(mixed $value, string $type): mixed
    {
        try {
            return match ($type) {
                Cast::INT->value => (int)$value,
                Cast::FLOAT->value => (float)$value,
                Cast::STRING->value => (string)$value,
                Cast::BOOLEAN->value => (bool)$value,
                Cast::ARRAY->value => (array)$value,
                Cast::OBJECT->value => (object)$value,
                default => $value,
            };
        } catch (Throwable) {
            return $value;
        }
    }
}