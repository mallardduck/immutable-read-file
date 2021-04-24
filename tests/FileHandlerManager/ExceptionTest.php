<?php

namespace MallardDuck\ImmutableReadFile\Tests\FileHandlerManager;

use MallardDuck\ImmutableReadFile\FileHandlerManager;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    private string $serializedExample = 'O:48:"MallardDuck\\ImmutableReadFile\\FileHandlerManager":3:{s:69:"' .
    "\0" . 'MallardDuck\\ImmutableReadFile\\FileHandlerManager' . "\0" . 'housekeepingCounter";i:1;s:71:"' .
    "\0" . 'MallardDuck\\ImmutableReadFile\\FileHandlerManager' . "\0" . 'fileHandlerReferences";a:0:{}s:70:"' .
    "\0" . 'MallardDuck\\ImmutableReadFile\\FileHandlerManager' . "\0" . 'fileHandlerInstances";a:0:{}}';


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
        $thing = $fileHandlerManager->getFileObjectFromPath(__DIR__ . '/../stubs/abcde.txt', $a);
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
