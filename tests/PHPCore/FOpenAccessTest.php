<?php

namespace MallardDuck\ImmutableReadFile\Tests\PHPCore;

use PHPUnit\Framework\TestCase;

/**
 * In regular file sockets, the function calls often have "impure" side-effects.
 * These tests will all be ones that would fail on regular file socket functions.
 */
class FOpenAccessTest extends TestCase
{
    private string $stubsDir = __DIR__ . '/../stubs/';

    public function testOutputOfCoreContents()
    {
        $step1 = fopen($this->stubsDir . 'json.txt', 'r');
        self::assertEquals('{"hello": "world"}', (string) stream_get_contents($step1));
    }

    public function testVerifyCoreFgetcFunctionality()
    {
        $step1 = fopen($this->stubsDir . 'abcde.txt', 'r');
        // First we test the effect of getting the whole content to string.
        self::assertEquals('abcde', (string) stream_get_contents($step1));
        self::assertTrue(feof($step1));
        // Then rewind for next test...
        rewind($step1);
        // Now we capture just one character...
        self::assertEquals('a', fgetc($step1));
        self::assertEquals('bcde', (string) stream_get_contents($step1));
        self::assertTrue(feof($step1));
        // Then pick up where we were before for next test...
        fseek($step1, 1);
        // And pick up where we left off before casting to string.
        self::assertEquals('b', fgetc($step1));
        self::assertEquals('c', fgetc($step1));
        self::assertEquals('d', fgetc($step1));
        self::assertFalse(feof($step1));
        // Then repeat the last character...
        fseek($step1, 3);
        self::assertEquals('d', fgetc($step1));
        self::assertEquals('e', fgetc($step1));
        self::assertFalse(fgetc($step1));
        self::assertTrue(feof($step1));
    }

    public function testVerifyCoreFgetsFunctionality()
    {
        $step1 = fopen($this->stubsDir . 'multi-line.txt', 'r');
        $expected1 = <<<EOF
Line 1\n
EOF;
        // Verify first line matches what we'd expect...
        // And the second line doesn't match...
        self::assertEquals($expected1, fgets($step1));
        self::assertNotEquals($expected1, fgets($step1));
        // Rewind for next test
        rewind($step1);
        // First verify the first line again...
        self::assertEquals($expected1, fgets($step1));
        // Then verify the second line for the first time without need to advance.
        self::assertEquals("Line 2", fgets($step1));
        // Check if we're at EOF...
        self::assertTrue(feof($step1));
    }

    public function testCoreGetPartialFile()
    {
        $step1 = fopen($this->stubsDir . 'json.txt', 'r');
        fseek($step1, 2);
        self::assertEquals('hello": "world"}', stream_get_contents($step1));
    }

    public function testCoreBytePositionFgetcLogic()
    {
        $step1 = fopen($this->stubsDir . 'json.txt', 'r');
        $res1 = fgetc($step1);

        self::assertEquals('{', $res1);
        self::assertNotEquals($res1, fgetc($step1));
        rewind($step1);
        self::assertEquals($res1, fgetc($step1));
    }

    public function testCanFreadStreamToken()
    {
        $step1 = fopen($this->stubsDir . 'json.txt', 'r');
        $res1 = fread($step1, 4);

        self::assertEquals('{"he', $res1);
        self::assertNotEquals($res1, fread($step1, 4));
        rewind($step1);
        self::assertEquals($res1, fread($step1, 4));
        self::assertEquals('llo"', fread($step1, 4));
    }

    public function testEmptyFileFopen()
    {
        $step1 = fopen($this->stubsDir . 'empty', 'r');
        self::assertFalse(feof($step1));
        self::assertFalse(fgetc($step1));
        self::assertTrue(feof($step1));
    }

    public function testNearlyEmptyFileFopen()
    {
        $step1 = fopen($this->stubsDir . 'space', 'r');
        self::assertFalse(feof($step1));
        self::assertEquals(' ', fgetc($step1));
        self::assertFalse(fgetc($step1));
        self::assertTrue(feof($step1));
    }
}
