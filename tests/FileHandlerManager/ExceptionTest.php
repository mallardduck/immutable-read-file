<?php

namespace MallardDuck\ImmutableReadFile\Tests\FileHandlerManager;

use MallardDuck\ImmutableReadFile\SharedManager\FileHandlerManager;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    private string $serializedExample = 'O:62:"MallardDuck\ImmutableReadFile\SharedManager\FileHandlerManager":0:{}';

    public function testCannotCloneInstance()
    {
        $fileHandlerManager = FileHandlerManager::instance();
        self::expectException(\Exception::class);
        self::expectExceptionMessage("Cannot clone a singleton.");
        $step1 = clone $fileHandlerManager;
    }

    public function testCannotSerialize()
    {
        $fileHandlerManager = FileHandlerManager::instance();
        $a = new \stdClass();
        $fileHandlerManager->getFileObjectFromPath(__DIR__ . '/../stubs/abcde.txt', $a);
        self::expectException(\Exception::class);
        self::expectExceptionMessage("Cannot serialize a singleton.");
        $serializedJazz = serialize($fileHandlerManager);
    }

    public function testCannotUnserialize()
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage("Cannot unserialize a singleton.");
        unserialize($this->serializedExample);
    }
}
