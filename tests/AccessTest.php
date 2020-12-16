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