<?php

namespace MallardDuck\ImmutaFopen\Tests;

use MallardDuck\ImmutaFopen\ImmutaFopen;
use PHPUnit\Framework\TestCase;

/**
 * In regular file sockets, the function calls often have "impure" side-effects.
 * These tests will all be ones that would fail on regular file socket functions.
 */
class AccessTest extends TestCase
{
    private string $filePath = __DIR__ . '/stubs/json.txt';

    public function testCanOpenBasicFilePath()
    {
        $step1 = ImmutaFopen::fromFilePath($this->filePath);
        self::assertEquals('{"hello": "world"}', (string) $step1);
    }

    public function testCanVerifyEndOfFile()
    {
        $step1 = ImmutaFopen::fromFilePath(__DIR__ . '/stubs/space');
        self::assertEquals(' ', (string) $step1);
        self::assertEquals(' ', $step1->fgetc());
        $step2 = $step1->advanceBytePosition();
        self::assertEquals('', (string) $step2);
        self::assertEquals('', $step2->fgetc());
        self::assertTrue($step2->eof());
        self::assertTrue($step2->feof());
    }

    public function testCanMultiLineEndOfFile()
    {
        $step1 = ImmutaFopen::fromFilePath(__DIR__ . '/stubs/multi-line.txt');
        $expected1 = <<<EOF
Line 1\n
EOF;
        self::assertEquals($expected1, $step1->fgets());
        self::assertEquals($expected1, $step1->fgets());

        $step2 = ImmutaFopen::recycleAtBytePosition($step1, strlen($expected1));
        self::assertEquals("Line 2", $step2->fgets());
        self::assertEquals("Line 2", (string) $step2);
        self::assertTrue($step2->eof());
        self::assertTrue($step2->feof());
    }

    public function testCanOpenFilePathWithPosition()
    {
        $step1 = ImmutaFopen::fromFilePathWithPosition($this->filePath, 2);
        self::assertEquals('hello": "world"}', (string) $step1);
    }

    public function testCanCastPositionedStreamToString()
    {
        $step1 = ImmutaFopen::recycleAtBytePosition(ImmutaFopen::fromFilePath($this->filePath), 2);
        self::assertEquals('hello": "world"}', (string) $step1);
        $step2 = ImmutaFopen::fromFilePathWithPosition($this->filePath, 2);
        self::assertEquals('hello": "world"}', (string) $step2);
    }

    public function testCanFgetcStreamToken()
    {
        $socket = ImmutaFopen::fromFilePath($this->filePath);
        $res1 = $socket->fgetc();
        self::assertEquals('{', $res1);
        self::assertEquals($res1, $socket->fgetc());
    }

    public function testCanFreadStreamToken()
    {
        $socket = ImmutaFopen::fromFilePath($this->filePath);
        $res1 = $socket->fread(4);
        self::assertEquals('{"he', $res1);
        self::assertEquals($res1, $socket->fread(4));
    }
}