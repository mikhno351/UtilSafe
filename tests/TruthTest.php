<?php

declare(strict_types=1);

use KetPHP\Utils\Truth;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KetPHP\Utils\Truth
 */
final class TruthTest extends TestCase
{
    protected function setUp(): void
    {
        Truth::configure();
    }

    public function testStrictMode(): void
    {
        $this->assertTrue(Truth::of(true, true));
        $this->assertTrue(Truth::of('true', true));
        $this->assertTrue(Truth::of(1, true));
        $this->assertTrue(Truth::of('1', true));

        $this->assertFalse(Truth::of('yes', true));
        $this->assertFalse(Truth::of(2, true));
        $this->assertFalse(Truth::of('on', true));
    }

    public function testDefaultTruthyStrings(): void
    {
        $this->assertTrue(Truth::of('yes'));
        $this->assertTrue(Truth::of('on'));
        $this->assertTrue(Truth::of('enabled'));
        $this->assertTrue(Truth::of('OK'));
        $this->assertFalse(Truth::of('nope'));
        $this->assertFalse(Truth::of(''));
    }

    public function testNumericValues(): void
    {
        $this->assertTrue(Truth::of(123));
        $this->assertTrue(Truth::of(0.1));
        $this->assertFalse(Truth::of(0));
        $this->assertFalse(Truth::of(-0.0));
    }

    public function testBooleanValues(): void
    {
        $this->assertTrue(Truth::of(true));
        $this->assertFalse(Truth::of(false));
    }

    public function testNullAndEmptyValues(): void
    {
        $this->assertFalse(Truth::of(null));
        $this->assertFalse(Truth::of(''));
        $this->assertFalse(Truth::of('   '));
    }

    public function testCustomTruthyList(): void
    {
        $custom = [42, 'affirmative', ['ok']];
        $this->assertTrue(Truth::of(42, false, $custom));
        $this->assertTrue(Truth::of('affirmative', false, $custom));
        $this->assertTrue(Truth::of(['ok'], false, $custom));
        $this->assertFalse(Truth::of('yes', false, $custom));
    }

    public function testConfigureMethod(): void
    {
        Truth::configure(['aye', 'roger', 'sure']);
        $this->assertTrue(Truth::of('aye'));
        $this->assertTrue(Truth::of('roger'));
        $this->assertFalse(Truth::of('yes'));
    }

    public function testObjectComparisonWithSameClass(): void
    {
        $a = new stdClass();
        $b = new stdClass();

        $this->assertTrue(Truth::of($a, false, [$b]));
    }

    public function testObjectComparisonWithDifferentClass(): void
    {
        $obj = new class () {};
        $this->assertFalse(Truth::of($obj, false, [new stdClass()]));
    }

    public function testObjectComparedToClassName(): void
    {
        $a = new stdClass();

        $this->assertTrue(Truth::of($a, false, [stdClass::class]));
        $this->assertTrue(Truth::of(stdClass::class, false, [$a]));
        $this->assertFalse(Truth::of($a, false, ['AnotherClass']));
    }

    public function testSameObjectReference(): void
    {
        $a = new stdClass();
        $this->assertTrue(Truth::of($a, false, [$a]));
    }

    public function testEdgeCases(): void
    {
        $this->assertFalse(Truth::of(['nested']));
        $this->assertFalse(Truth::of(tmpfile()));
        $this->assertFalse(Truth::of(fopen('php://memory', 'r')));
    }
}