<?php

namespace MallardDuck\Tests\ImmutaFopen;

use MallardDuck\ImmutaFopen\ImmutaFopen;
use PHPUnit\Framework\TestCase;

/**
 * In regular file sockets, the function calls often have "impure" side-effects.
 * These tests will all be ones that would fail on regular file socket functions.
 */
class InfectionTest extends TestCase
{
    private string $stubsDir = __DIR__ . '/../stubs/';

    /**
     * This test ensures that the internal "unreadBytesSize" logic is never wrong.
     */
    public function testEnsureProperUnreadSize()
    {
        $filePath = $this->stubsDir . 'json.txt';
        $socket = ImmutaFopen::fromFilePath($filePath);
        self::assertEquals(18, $socket->getUnreadBytesSize());
        $step2 = ImmutaFopen::recycleAtBytePosition($socket, 7);
        self::assertEquals(11, $step2->getUnreadBytesSize());
        $step3 = ImmutaFopen::recycleAtBytePosition($step2, 5);
        $expect = $step3->getFileSize() - $step3->getBytePosition();
        self::assertEquals(11, $step2->getUnreadBytesSize());
    }

    /**
     * This test ensures that string casting doesn't affect position.
     */
    public function testEnsureStringOututIsIdempotent()
    {
        $filePath = $this->stubsDir . 'json.txt';
        $socket = ImmutaFopen::fromFilePath($filePath);
        self::assertEquals(0, $socket->getBytePosition());
        self::assertEquals('{', $socket->fgetc());
        $s = (string) $socket;
        self::assertEquals('{', $socket->fgetc());
        self::assertEquals(0, $socket->getBytePosition());
    }

    /**
     * This test ensures that 'resetToCanonicalPosition' is actually rewinding.
     */
    public function testEnsureCanonicalRewinds()
    {
        $filePath = $this->stubsDir . 'json.txt';
        $socket = ImmutaFopen::fromFilePath($filePath);
        $step2 = ImmutaFopen::recycleAtBytePosition($socket, 3);
        $expected = "el";
        $r1 = $step2->fread(2);
        self::assertEquals($expected, $r1);
        $r2 = $step2->fread(2);
        self::assertEquals($expected, $r2);
        self::assertEquals($r1, $r2);
    }
}