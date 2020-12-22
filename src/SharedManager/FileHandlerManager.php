<?php

namespace MallardDuck\ImmutableReadFile\SharedManager;

use SplFileObject;

/**
 * Class FileHandlerManager
 * @package MallardDuck\ImmutableReadFile\SharedManager
 * @internal
 */
final class FileHandlerManager
{
    private const HOUSEKEEPING_EVERY = 3;
    private int $housekeepingCounter = 0;

    private static ?FileHandlerManager $instance;

    /**
     * @var array<mixed, array<int, string>>
     */
    private array $fileHandlerReferences = [];

    /**
     * @var array<mixed, \WeakReference<\SplFileObject>|null>
     */
    private array $fileHandlerInstances = [];

    public static function getSplFileObjectFromPath(string $filePath, object $requestingObject): SplFileObject
    {
        return self::instance()->getFileObjectFromPath($filePath, $requestingObject);
    }

    public static function freeSplFileObjectFromPath(string $filePath, object $requestingObject): void
    {
        self::instance()->freeFileObjectFromPath($filePath, $requestingObject);
    }

    public static function instance(): FileHandlerManager
    {
        if (!isset(self::$instance)) {
            self::$instance = new FileHandlerManager();
        }
        self::$instance->housekeeping();

        return self::$instance;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct()
    {
    }

    public function getFileObjectFromPath(string $filePath, object $requestingObject): SplFileObject
    {
        $this->housekeeping();
        $normalizedPath = $this->normalizeFilePath($filePath);
        if (!isset($this->fileHandlerInstances[$normalizedPath])) {
            $tempVar = new SplFileObject($normalizedPath, 'r');
            $tempVar->setFlags(SplFileObject::READ_AHEAD);
            $this->fileHandlerReferences[$normalizedPath][spl_object_id($requestingObject)] = $filePath;
            $this->fileHandlerInstances[$normalizedPath] = \WeakReference::create($tempVar);
        } else {
            $this->fileHandlerReferences[$normalizedPath][spl_object_id($requestingObject)] = $filePath;
        }

        /** @var SplFileObject $weakRef */
        $weakRef = $this->fileHandlerInstances[$normalizedPath]->get();
        return $weakRef;
    }

    public function freeFileObjectFromPath(string $filePath, object $requestingObject): void
    {
        $normalizedPath = $this->normalizeFilePath($filePath);
        if (isset($this->fileHandlerReferences[$normalizedPath])) {
            unset($this->fileHandlerReferences[$normalizedPath][spl_object_id($requestingObject)]);
            $remainingCount = count($this->fileHandlerReferences[$normalizedPath]);
            if (0 === $remainingCount) {
                if (isset($this->fileHandlerInstances[$normalizedPath])) {
                    unset($this->fileHandlerInstances[$normalizedPath]);
                }
                unset($this->fileHandlerReferences[$normalizedPath]);
            }
        }
    }

    /**
     * @internal
     */
    public function forceFlush(): void
    {
        $this->housekeeping(true);
    }

    private function housekeeping(bool $force = false) : void
    {
        if ($force || (++$this->housekeepingCounter === self::HOUSEKEEPING_EVERY)) {
            foreach ($this->fileHandlerInstances as $id => $weakRef) {
                if (null === $weakRef || $weakRef->get() === null) {
                    unset(
                        $this->fileHandlerInstances[$id],
                        $this->fileHandlerReferences[$id]
                    );
                }
            }

            $this->housekeepingCounter = 0;
        }
    }

    /**
     * @param string $filePath
     */
    public function closeHandlerFromPath(string $filePath): void
    {
        $normalizedPath = $this->normalizeFilePath($filePath);
        if (isset($this->fileHandlerInstances[$normalizedPath])) {
            $this->fileHandlerInstances[$normalizedPath] = null;
            unset($this->fileHandlerInstances[$normalizedPath]);
        }
        if (isset($this->fileHandlerReferences[$normalizedPath])) {
            unset($this->fileHandlerReferences[$normalizedPath]);
        }
    }

    private function normalizeFilePath(string $filePath): string
    {
        return realpath($filePath);
    }

    /**
     * Singletons should not be cloneable.
     */
    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * Singletons should not be serializable to strings.
     */
    public function __sleep()
    {
        throw new \Exception("Cannot serialize a singleton.");
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}
