<?php

namespace MallardDuck\Tests\ImmutaFopen;

use MallardDuck\ImmutaFopen\Exceptions\InvalidFilePathException;
use MallardDuck\ImmutaFopen\ImmutaFopen;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    private string $stubsDir = __DIR__ . '/../stubs/';

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

    public function testEmptyFilePathWithPositionThrowsException()
    {
        self::expectException(InvalidFilePathException::class);
        ImmutaFopen::fromFilePathWithPosition("", 4);

        self::expectException(InvalidFilePathException::class);
        ImmutaFopen::fromFilePathWithPosition("test.txt", 4);
    }

    public function testCanLoadFromFile()
    {
        $filePath = $this->stubsDir . 'json.txt';
        $socket = ImmutaFopen::fromFilePath($filePath);
        self::assertEquals(__DIR__ . '/../stubs/json.txt', $socket->getFilePath());
        self::assertEquals('txt', $socket->getExtension());
        self::assertEquals(0, $socket->getBytePosition());
        self::assertEquals(18, $socket->getFileSize());
        self::assertEquals(18, $socket->getUnreadBytesSize());
    }

    public function testEmptyFileToString()
    {
        $socket = ImmutaFopen::fromFilePath($this->stubsDir . 'empty');
        self::assertEquals("", (string) $socket);
    }
}