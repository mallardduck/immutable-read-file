<?php

namespace MallardDuck\ImmutaFopen\Tests;

use MallardDuck\ImmutaFopen\Exceptions\InvalidFilePathException;
use MallardDuck\ImmutaFopen\ImmutaFopen;

class ExceptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorThrowsException()
    {
        self::expectException(\Error::class);
        new ImmutaFopen();
    }

    public function testEmptyFilePathThrowsException()
    {
        self::expectException(InvalidFilePathException::class);
        ImmutaFopen::fromFilePath("");

        self::expectException(InvalidFilePathException::class);
        ImmutaFopen::fromFilePath("test.txt");
    }

    public function testCanLoadFromFile()
    {
        $filePath = __DIR__ . '/stubs/json.txt';
        $socket = ImmutaFopen::fromFilePath($filePath);
        self::assertEquals(__DIR__ . '/stubs/json.txt', $socket->getFilePath());
        self::assertEquals('txt', $socket->getExtension());
        self::assertEquals(0, $socket->getBytePosition());
        self::assertEquals(18, $socket->getFileSize());
        self::assertEquals(18, $socket->getUnreadBytesSize());
    }

    public function testEmptyFileToString()
    {
        $socket = ImmutaFopen::fromFilePath(__DIR__ . '/stubs/empty');
        self::assertEquals("<EMPTYFILE>", (string) $socket);
    }
}