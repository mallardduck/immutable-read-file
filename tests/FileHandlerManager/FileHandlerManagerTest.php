<?php

namespace MallardDuck\ImmutableReadFile\Tests\FileHandlerManager;

use MallardDuck\ImmutableReadFile\FileHandlerManager;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class FileHandlerManagerTest extends TestCase
{
    private ?FileHandlerManager $fileHandlerManager;
    private string $stubsDir = __DIR__ . '/../stubs/';

    public function setUp(): void
    {
        // We set to null and re-ask for it to kill the old singleton for a new one.
        $this->fileHandlerManager = FileHandlerManager::instance();
        $this->fileHandlerManager->forceFlush(); // Only use this in test context to keep things clean.
        parent::setUp();
    }

    public function testHasFileHandlerManagerInstance()
    {
        self::assertEquals(FileHandlerManager::instance(), $this->fileHandlerManager);
    }

    public function testCanLoadTheSameFileTwice()
    {
        $a = new \stdClass();
        $abcdeFile1 = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'abcde.txt', $a);
        $abcdeFile2 = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'abcde.txt', $a);
        self::assertEquals($abcdeFile1, $abcdeFile2);
        self::assertSame($abcdeFile1, $abcdeFile2);
        FileHandlerManager::instance()->closeHandlerFromPath($this->stubsDir . 'abcde.txt');
    }

    public function testCanCloseByFilePath()
    {
        $a = new \stdClass();
        $abcdeFile1 = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'abcde.txt', $a);
        self::assertSame("abcde", $abcdeFile1->fread($abcdeFile1->getSize()));
        FileHandlerManager::instance()->closeHandlerFromPath($this->stubsDir . 'abcde.txt');
        unset($abcdeFile1);
    }

    public function testCanGetSplFile()
    {
        $a = new \stdClass();
        $jsonFile = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'json.txt', $a);
        self::assertIsObject($jsonFile);
        self::assertEquals(SplFileObject::class, get_class($jsonFile));
        $expectingBaseDir = realpath($this->stubsDir);
        self::assertEquals($expectingBaseDir, $jsonFile->getPath());
        self::assertEquals("json.txt", $jsonFile->getFilename());
        self::assertEquals($expectingBaseDir . DIRECTORY_SEPARATOR . "json.txt", $jsonFile->getRealPath());
        self::assertEquals(18, $jsonFile->getSize());
        self::assertEquals('{"hello": "world"}', $jsonFile->fread($jsonFile->getSize()));
        // NOTE: Everything but this is the same since it's a shared SplFileObject
        self::assertNotEquals('{"hello": "world"}', $jsonFile->fread($jsonFile->getSize()));
        self::assertEquals('', $jsonFile->fread($jsonFile->getSize()));
    }
}
