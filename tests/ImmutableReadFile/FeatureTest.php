<?php

namespace MallardDuck\Tests\ImmutableReadFile;

use MallardDuck\ImmutableReadFile\ImmutableFile;
use PHPUnit\Framework\TestCase;

/**
 * These tests any "special" non reading features.
 */
class FeatureTest extends TestCase
{
    private string $filePath = __DIR__ . '/../stubs/json.txt';

    public function testCanCastStreamToString()
    {
        $socket = ImmutableFile::fromFilePath($this->filePath);
        self::assertEquals('{"hello": "world"}', (string) $socket);
    }

    public function testAdvancePositionFeature()
    {
        $step1 = ImmutableFile::fromFilePath($this->filePath);
        self::assertEquals('{', $step1->fgetc());
        self::assertEquals('{"hello": "world"}', (string) $step1);
        self::assertEquals('{', $step1->fgetc());
        $step2 = $step1->advanceBytePosition();
        self::assertNotEquals($step1, $step2);
        self::assertEquals('"', $step2->fgetc());
        self::assertEquals('"hello": "world"}', (string) $step2);
        self::assertEquals('"', $step2->fgetc());
    }
}