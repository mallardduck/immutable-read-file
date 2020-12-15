<?php

namespace MallardDuck\ImmutaFopen\Tests;

use MallardDuck\ImmutaFopen\Exceptions\InvalidFilePathException;
use MallardDuck\ImmutaFopen\ImmutaFopen;

/**
 * In regular file sockets, the function calls often have "impure" side-effects.
 * These tests will all be ones that would fail on regular file socket functions.
 */
class AccessTest extends \PHPUnit\Framework\TestCase
{
    private string $filePath = __DIR__ . '/stubs/json.txt';

    public function testCanCastStreamToString()
    {
        $socket = ImmutaFopen::fromFilePath($this->filePath);
        self::assertEquals('{"hello": "world"}', (string) $socket);
    }

    public function testCanCastPositionedStreamToString()
    {
        $socket = ImmutaFopen::fromFilePath($this->filePath);
        self::assertEquals('{"hello": "world"}', (string) $socket);
        $newSocket = ImmutaFopen::recycleAtBytePosition($socket, 2);
        self::assertEquals('hello": "world"}', (string) $newSocket);
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