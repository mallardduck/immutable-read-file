<?php declare(strict_types=1);

namespace MallardDuck\ImmutaFopen;

use MallardDuck\ImmutaFopen\Exceptions\InvalidFilePathException;
use SplFileObject;

class ImmutaFopen
{
    /**
     * @psalm-immutable
     */
    private string $filePath;
    /**
     * @var SplFileObject
     * @psalm-readonly-allow-private-mutation
     */
    private ?SplFileObject $fileHandler;
    /**
     * @psalm-immutable
     */
    private int $bytePosition;

    /**
     * @param string $filePath Must be an absolute path to a real file.
     * @return ImmutaFopen
     */
    public static function fromFilePath(string $filePath): ImmutaFopen
    {
        if (!is_file($filePath)) {
            throw new InvalidFilePathException("The file path provided does not point to a valid file.");
        }
        return new ImmutaFopen($filePath);
    }

    public static function fromFilePathWithPosition(string $filePath, int $bytePosition): ImmutaFopen
    {
        if (!is_file($filePath)) {
            throw new InvalidFilePathException("The file path provided does not point to a valid file.");
        }
        return new ImmutaFopen($filePath, $bytePosition);
    }

    public static function recycleAtBytePosition(self $existingSocket, int $bytePosition): ImmutaFopen
    {
        return new ImmutaFopen($existingSocket->getFilePath(), $bytePosition);
    }

    private function __construct(string $filePath, ?int $bytePosition = null)
    {
        $this->filePath = $filePath;
        $this->fileHandler = new SplFileObject($filePath, 'r');
        $this->bytePosition = $bytePosition ?? 0;
        if (null !== $bytePosition) {
            $this->resetToCanonicalPosition();
        }
    }

    public function __destruct()
    {
        $this->fileHandler = null;
    }

    private function resetToCanonicalPosition(): void
    {
        $this->fileHandler->fseek($this->bytePosition);
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
        $token = $this->fileHandler->fgetc();
        $this->resetToCanonicalPosition();
        return $token;
    }

    public function fread(int $readLength): string
    {
        $token = $this->fileHandler->fread($readLength);
        $this->resetToCanonicalPosition();
        return $token;
    }

    public function advanceBytePosition(int $advanceSteps = 1): ImmutaFopen
    {
        return ImmutaFopen::recycleAtBytePosition($this, $this->getBytePosition() + $advanceSteps);
    }

    public function __toString(): string
    {
        if (0 === $this->getFileSize()) {
            return "";
        }

        $stringOut = $this->fileHandler->fread($this->getFileSize());
        $this->resetToCanonicalPosition();
        return $stringOut;
    }
}
