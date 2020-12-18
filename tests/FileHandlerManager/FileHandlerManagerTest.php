<?php

namespace MallardDuck\ImmutableReadFile\Tests\FileHandlerManager;

use MallardDuck\ImmutableReadFile\SharedManager\FileHandlerManager;
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
        parent::setUp();
    }

    public function testHasFileHandlerManagerInstance()
    {
        self::assertEquals(FileHandlerManager::instance(), $this->fileHandlerManager);
    }

    public function testCanLoadTheSameFileTwice()
    {
        $abcdeFile1 = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'abcde.txt');
        $abcdeFile2 = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'abcde.txt');
        self::assertEquals($abcdeFile1, $abcdeFile2);
        self::assertSame($abcdeFile1, $abcdeFile2);
    }

    public function testCanGetSplFile()
    {
        $jsonFile = $this->fileHandlerManager->getFileObjectFromPath($this->stubsDir . 'json.txt');
        self::assertIsObject($jsonFile);
        self::assertEquals(SplFileObject::class, get_class($jsonFile));
        self::assertEquals("/Users/danpock/GitProjects/immutafopen/tests/stubs", $jsonFile->getPath());
        self::assertEquals("json.txt", $jsonFile->getFilename());
        self::assertEquals("/Users/danpock/GitProjects/immutafopen/tests/stubs/json.txt", $jsonFile->getRealPath());
        self::assertEquals(18, $jsonFile->getSize());
        self::assertEquals('{"hello": "world"}', $jsonFile->fread($jsonFile->getSize()));
        // NOTE: Everything but this is the same since it's a shared SplFileObject
        self::assertNotEquals('{"hello": "world"}', $jsonFile->fread($jsonFile->getSize()));
        self::assertEquals('', $jsonFile->fread($jsonFile->getSize()));
    }

}