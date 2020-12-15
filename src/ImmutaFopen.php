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
     * @var resource
     */
    private $fileHandler;
    /**
     * @psalm-immutable
     */
    private int $bytePosition;

    /**
     * @param string $filePath Must be an absolute path to a real file.
     * @return static
     */
    public static function fromFilePath(string $filePath): self
    {
        if (!is_file($filePath)) {
            throw new InvalidFilePathException("The file path provided does not point to a valid file.");
        }
        return new self($filePath);
    }

    public static function recycleAtBytePosition(self $existingSocket, int $bytePosition)
    {
        return new self($existingSocket->getFilePath(), $bytePosition);
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

    public function __toString(): string
    {
        if (0 === $this->getFileSize()) {
            return "<EMPTYFILE>";
        }

        $stringOut = $this->fileHandler->fread($this->fileHandler->getSize());
        $this->resetToCanonicalPosition();
        return $stringOut;
    }
}
