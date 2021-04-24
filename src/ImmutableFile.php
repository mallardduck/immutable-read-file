<?php

declare(strict_types=1);

namespace MallardDuck\ImmutableReadFile;

use MallardDuck\ImmutableReadFile\Exceptions\InvalidFilePathException;
use MallardDuck\ImmutableReadFile\SharedManager\FileHandlerManager;
use SplFileObject;

class ImmutableFile
{
    /**
     * @psalm-immutable
     */
    private string $filePath;
    /**
     * @psalm-readonly-allow-private-mutation
     */
    private ?SplFileObject $fileHandler;
    /**
     * @psalm-immutable
     */
    private int $bytePosition = 0;

    /**
     * @psalm-readonly-allow-private-mutation
     */
    private bool $atEndOfFile = false;

    private function __construct(string $filePath, ?int $bytePosition = null)
    {
        $this->filePath = $filePath;
        $this->fileHandler = FileHandlerManager::getSplFileObjectFromPath($this->filePath, $this);
        if ($bytePosition !== null) {
            $this->bytePosition = $bytePosition;
            $this->resetToCanonicalPosition();
        }
        $this->endOfFileSanityCheck();
    }

    public function __destruct()
    {
        FileHandlerManager::freeSplFileObjectFromPath($this->filePath, $this);
        $this->fileHandler = null;
    }

    public function __toString(): string
    {
        $this->resetToCanonicalPosition();
        if ($this->getFileSize() === 0) {
            return '';
        }

        $stringOut = $this->fileHandler->fread($this->getFileSize());
        $this->resetToCanonicalPosition();
        return $stringOut;
    }

    /**
     * @param string $filePath Must be an absolute path to a real file.
     */
    public static function fromFilePath(string $filePath): ImmutableFile
    {
        if (! is_file($filePath)) {
            throw new InvalidFilePathException('The file path provided does not point to a valid file.');
        }
        return new ImmutableFile($filePath);
    }

    public static function fromFilePathWithPosition(string $filePath, int $bytePosition): ImmutableFile
    {
        if (! is_file($filePath)) {
            throw new InvalidFilePathException('The file path provided does not point to a valid file.');
        }
        return new ImmutableFile($filePath, $bytePosition);
    }

    public static function recycleAtBytePosition(self $existingSocket, int $bytePosition): ImmutableFile
    {
        return new ImmutableFile($existingSocket->getFilePath(), $bytePosition);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getExtension(): string
    {
        return $this->fileHandler->getExtension();
    }

    public function getBytePosition(): int
    {
        return $this->bytePosition;
    }

    public function getFileSize(): int
    {
        return intval($this->fileHandler->getSize());
    }

    public function getUnreadBytesSize(): int
    {
        return $this->getFileSize() - $this->bytePosition;
    }

    public function fgetc(): string
    {
        $this->resetToCanonicalPosition();
        $token = (string) $this->fileHandler->fgetc();
        $this->resetToCanonicalPosition();
        return $token;
    }

    public function fread(int $readLength): string
    {
        $this->resetToCanonicalPosition();
        $token = $this->fileHandler->fread($readLength);
        $this->resetToCanonicalPosition();
        return $token;
    }

    public function fgets(): string
    {
        $this->resetToCanonicalPosition();
        $line = $this->fileHandler->fgets();
        $this->resetToCanonicalPosition();
        return $line;
    }

    public function eof(): bool
    {
        return $this->fileHandler->eof();
    }

    public function feof(): bool
    {
        return $this->atEndOfFile;
    }

    /**
     * Advance the byte position of this ImmutableFile - either by one byte, or the number provided.
     *
     * Since it's an immutable file you actually get a new entity of the same type.
     * This is effectively equivalent to fseek($fh, X, SEEK_CUR) - but based on this entity.
     * The X would be based on the canonical position of the entity + $advanceSteps.
     */
    public function advanceBytePosition(int $advanceSteps = 1): ImmutableFile
    {
        // TODO: consider if this should throw the current entity is EOF already.
        return ImmutableFile::recycleAtBytePosition($this, $this->getBytePosition() + $advanceSteps);
    }

    private function resetToCanonicalPosition(): void
    {
        $this->fileHandler->fseek($this->bytePosition);
    }

    private function endOfFileSanityCheck(): void
    {
        $res = $this->fileHandler->fgetc();
        if ($res === false) {
            $this->atEndOfFile = true;
        }
        $this->resetToCanonicalPosition();
    }
}
