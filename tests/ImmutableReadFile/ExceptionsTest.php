<?php

namespace MallardDuck\ImmutableReadFile\Tests\ImmutableReadFile;

use MallardDuck\ImmutableReadFile\ImmutableFile;
use MallardDuck\ImmutableReadFile\InvalidFilePath;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    private string $stubsDir = __DIR__ . '/../stubs/';

    public function testConstructorThrowsException()
    {
        $this->expectException(\Error::class);
        new ImmutableFile();
    }

    public function testEmptyFilePathThrowsException()
    {
        $this->expectException(InvalidFilePath::class);
        ImmutableFile::fromFilePath("");

        $this->expectException(InvalidFilePath::class);
        ImmutableFile::fromFilePath("test.txt");
    }

    public function testEmptyFilePathWithPositionThrowsException()
    {
        $this->expectException(InvalidFilePath::class);
        ImmutableFile::fromFilePathWithPosition("", 4);

        $this->expectException(InvalidFilePath::class);
        ImmutableFile::fromFilePathWithPosition("test.txt", 4);
    }

    public function testCanLoadFromFile()
    {
        $filePath = $this->stubsDir . 'json.txt';
        $socket = ImmutableFile::fromFilePath($filePath);
        self::assertEquals(__DIR__ . '/../stubs/json.txt', $socket->getFilePath());
        self::assertEquals('txt', $socket->getExtension());
        self::assertEquals(0, $socket->getBytePosition());
        self::assertEquals(18, $socket->getFileSize());
        self::assertEquals(18, $socket->getUnreadBytesSize());
    }

    public function testEmptyFileToString()
    {
        $socket = ImmutableFile::fromFilePath($this->stubsDir . 'empty');
        self::assertEquals("", (string) $socket);
    }
}
