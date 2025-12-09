<?php

declare(strict_types=1);

use KetPHP\Utils\Common\Cast;
use KetPHP\Utils\Safe;
use PHPUnit\Framework\TestCase;

final class SafeTest extends TestCase
{

    public function testReturnsDirectValue(): void
    {
        $value = Safe::get('Hello');
        $this->assertSame('Hello', $value);
    }

    public function testReturnsDirectValueTrim(): void
    {
        $value = Safe::get('   Hello   ', null, 'trim');
        $this->assertSame('Hello', $value);
    }

    public function testReturnsCallableValue(): void
    {
        $value = Safe::get(fn() => 123);
        $this->assertSame(123, $value);
    }

    public function testReturnsDefaultIfValueIsNull(): void
    {
        $value = Safe::get(null, 'Default');
        $this->assertSame('Default', $value);
    }

    public function testReturnsDefaultIfCallableThrows(): void
    {
        $value = Safe::get(function () {
            throw new Exception('Boom');
        }, 'Fallback');
        $this->assertSame('Fallback', $value);
    }

    public function testTransformIsAppliedOnlyWhenValueUsed(): void
    {
        $transformCalled = false;
        $value = Safe::get('data', 'Default', function ($v) use (&$transformCalled) {
            $transformCalled = true;
            return strtoupper($v);
        });

        $this->assertTrue($transformCalled);
        $this->assertSame('DATA', $value);
    }

    public function testTransformIgnoredWhenDefaultUsed(): void
    {
        $transformCalled = false;
        $value = Safe::get(null, 'Default', function ($v) use (&$transformCalled) {
            $transformCalled = true;
            return strtoupper($v);
        });

        $this->assertFalse($transformCalled);
        $this->assertSame('Default', $value);
    }

    public function testCastsValuesProperly(): void
    {
        $this->assertSame(123, Safe::get('123', null, null, Cast::INT));
        $this->assertSame(123.0, Safe::get('123', null, null, Cast::FLOAT));
        $this->assertSame('1', Safe::get(true, null, null, Cast::STRING));
        $this->assertSame(true, Safe::get(1, null, null, Cast::BOOLEAN));
        $this->assertSame(['x' => 1], Safe::get(['x' => 1], null, null, Cast::ARRAY));
        $this->assertIsObject(Safe::get(['x' => 1], null, null, Cast::OBJECT));
    }

    public function testInvalidCastDoesNotThrow(): void
    {
        $value = Safe::get(tmpfile(), null, null, 'unknown-type');
        $this->assertIsResource($value);
    }

    public function testTransformExceptionDoesNotThrow(): void
    {
        $value = Safe::get('test', null, function () {
            throw new Exception('transform error');
        });
        $this->assertSame('test', $value);
    }

    public function testDefaultCallableExecutedSafely(): void
    {
        $value = Safe::get(null, fn() => 'fallback');
        $this->assertSame('fallback', $value);
    }

    public function testDefaultCallableWithExceptionReturnsNull(): void
    {
        $value = Safe::get(null, function () {
            throw new Exception('oops');
        });
        $this->assertNull($value);
    }

    public function testNonExistingVariableHandledSafely(): void
    {
        // Adding "null" to remove the warnings: $data['unknown'] ?? null

        $data = ['known' => 'value'];

        $this->assertSame('default-value', Safe::get(@$data['unknown'], 'default-value'));
        $this->assertSame('value', Safe::get(@$data['known'], 'default-value'));
    }
}