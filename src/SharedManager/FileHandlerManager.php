<?php

namespace MallardDuck\ImmutableReadFile\SharedManager;

use SplFileObject;

final class FileHandlerManager
{
    /**
     * @var FileHandlerManager|null
     */
    private static $instance;

    /**
     * @var array<mixed, SplFileObject>
     */
    private static array $fileHandlerInstances = [];

    public static function instance(): FileHandlerManager
    {
        if (!isset(self::$instance)) {
            self::$instance = new FileHandlerManager();
        }

        return self::$instance;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct() { }

    public function getFileObjectFromPath(string $filePath): SplFileObject
    {
        $normalizedPath = $this->normalizeFilePath($filePath);
        if (!isset(self::$fileHandlerInstances[$normalizedPath])) {
            self::$fileHandlerInstances[$normalizedPath] = new SplFileObject($normalizedPath, 'r');
        }
        return self::$fileHandlerInstances[$normalizedPath];
    }

    private function normalizeFilePath(string $filePath): string
    {
        return realpath($filePath);
    }

    /**
     * Singletons should not be cloneable.
     */
    public function __clone() {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}