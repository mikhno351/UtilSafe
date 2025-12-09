<?php

declare(strict_types=1);

use KetPHP\Utils\Truth;
use PHPUnit\Framework\TestCase;

final class TruthTest extends TestCase
{

    public function testStrictMode(): void
    {
        $this->assertTrue(Truth::of(true, true));
        $this->assertTrue(Truth::of(1, true));
        $this->assertTrue(Truth::of('1', true));
        $this->assertTrue(Truth::of('true', true));

        $this->assertFalse(Truth::of(false, true));
        $this->assertFalse(Truth::of(0, true));
        $this->assertFalse(Truth::of('0', true));
        $this->assertFalse(Truth::of('false', true));
    }

    public function testNonStrictMode(): void
    {
        $this->assertTrue(Truth::of(true));
        $this->assertTrue(Truth::of(1));
        $this->assertTrue(Truth::of('1'));
        $this->assertTrue(Truth::of('true'));
        $this->assertTrue(Truth::of('on'));
        $this->assertTrue(Truth::of('yes'));

        $this->assertFalse(Truth::of(false));
        $this->assertFalse(Truth::of(0));
        $this->assertFalse(Truth::of('0'));
        $this->assertFalse(Truth::of('no'));
        $this->assertFalse(Truth::of('active'));
        $this->assertFalse(Truth::of(null));
    }

    public function testCustomTruthyValues(): void
    {
        $custom = ['foo', 123, true];

        $this->assertTrue(Truth::of('foo', false, $custom));
        $this->assertTrue(Truth::of(123, false, $custom));
        $this->assertTrue(Truth::of(true, false, $custom));
        $this->assertFalse(Truth::of('bar', false, $custom));
        $this->assertFalse(Truth::of(0, false, $custom));
    }

    public function testConfigureGlobalTruthyValues(): void
    {
        Truth::configure(['yes', 'sure']);

        $this->assertTrue(Truth::of('yes'));
        $this->assertTrue(Truth::of('sure'));
        $this->assertFalse(Truth::of('on'));
    }

    public function testHandlesUnexpectedTypesSafely(): void
    {
        $this->assertFalse(Truth::of([]));
        $this->assertFalse(Truth::of(new stdClass()));
        $this->assertFalse(Truth::of(fopen('php://memory', 'r')));
    }
}