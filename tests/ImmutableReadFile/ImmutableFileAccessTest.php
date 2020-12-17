<?php

namespace MallardDuck\Tests\ImmutableReadFile;

use MallardDuck\ImmutableReadFile\ImmutableFile;
use PHPUnit\Framework\TestCase;

/**
 * In regular file sockets, the function calls often have "impure" side-effects.
 * These tests will all be ones that would fail on regular file socket functions.
 */
class ImmutableFileAccessTest extends TestCase
{
    private string $stubsDir = __DIR__ . '/../stubs/';

    public function testCanOpenBasicFilePath()
    {
        $step1 = ImmutableFile::fromFilePath($this->stubsDir . 'json.txt');
        self::assertEquals('{"hello": "world"}', (string) $step1);
    }

    public function testCanVerifyEndOfFile()
    {
        $step1 = ImmutableFile::fromFilePath($this->stubsDir . 'abcde.txt');
        self::assertEquals('abcde', (string) $step1);
        self::assertEquals('a', $step1->fgetc());
        self::assertFalse($step1->eof());
        $step2 = $step1->advanceBytePosition();
        self::assertEquals('bcde', (string) $step2);
        self::assertEquals('b', $step2->fgetc());
        self::assertFalse($step2->eof());
        $step3 = $step2->advanceBytePosition(3);
        self::assertEquals('e', (string) $step3);
        self::assertEquals('e', $step3->fgetc());
        self::assertTrue($step3->advanceBytePosition()->feof());
    }

    public function testCanMultiLineEndOfFile()
    {
        $step1 = ImmutableFile::fromFilePath($this->stubsDir . 'multi-line.txt');
        $expected1 = <<<EOF
Line 1\n
EOF;
        self::assertEquals($expected1, $step1->fgets());
        self::assertEquals($expected1, $step1->fgets());

        $step2 = ImmutableFile::recycleAtBytePosition($step1, strlen($expected1));
        self::assertEquals("Line 2", $step2->fgets());
        self::assertEquals("Line 2", (string) $step2);

        $step3 = $step2->advanceBytePosition(strlen((string) $step2));
        self::assertTrue($step3->advanceBytePosition()->feof());
    }

    public function testCanOpenFilePathWithPosition()
    {
        $step1 = ImmutableFile::fromFilePathWithPosition($this->stubsDir . 'json.txt', 2);
        self::assertEquals('hello": "world"}', (string) $step1);
    }

    public function testCanCastPositionedStreamToString()
    {
        $step1 = ImmutableFile::recycleAtBytePosition(ImmutableFile::fromFilePath($this->stubsDir . 'json.txt'), 2);
        self::assertEquals('hello": "world"}', (string) $step1);
        $step2 = ImmutableFile::fromFilePathWithPosition($this->stubsDir . 'json.txt', 2);
        self::assertEquals('hello": "world"}', (string) $step2);
    }

    public function testCanFgetcStreamToken()
    {
        $socket = ImmutableFile::fromFilePath($this->stubsDir . 'json.txt');
        $res1 = $socket->fgetc();
        self::assertEquals('{', $res1);
        self::assertEquals($res1, $socket->fgetc());
    }

    public function testCanFreadStreamToken()
    {
        $socket = ImmutableFile::fromFilePath($this->stubsDir . 'json.txt');
        $res1 = $socket->fread(4);
        self::assertEquals('{"he', $res1);
        self::assertEquals($res1, $socket->fread(4));
    }
}