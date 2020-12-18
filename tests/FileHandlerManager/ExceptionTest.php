<?php

namespace MallardDuck\ImmutableReadFile\Tests\FileHandlerManager;

use MallardDuck\ImmutableReadFile\SharedManager\FileHandlerManager;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class ExceptionTest extends TestCase
{

    public function testCannotCloneInstance()
    {
        $fileHandlerManager = FileHandlerManager::instance();
        self::expectException(\Exception::class);
        self::expectExceptionMessage("Cannot clone a singleton.");
        $step1 = clone $fileHandlerManager;
    }

    public function testCannotSerializeAndUnserialize()
    {
        $fileHandlerManager = FileHandlerManager::instance();
        $fileHandlerManager->getFileObjectFromPath(__DIR__ . '/../stubs/abcde.txt');
        $serializedJazz = serialize($fileHandlerManager);
        self::expectException(\Exception::class);
        self::expectExceptionMessage("Cannot unserialize a singleton.");
        unserialize($serializedJazz);
    }
}