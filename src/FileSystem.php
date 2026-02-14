<?php

namespace Phrity\FileSystem;

use Phrity\Util\ErrorHandler;

class FileSystem
{
    private ErrorHandler $errorHandler;

    public function __construct()
    {
        $this->errorHandler = new ErrorHandler();
    }

    /* ---------- Check methods -------------------------------------------- */

    public function exists(Path|string $path): bool
    {
        return $this->errorHandler->with(function () use ($path) {
            return file_exists($path);
        }, function () {
            return false; // False on E_WARNING
        });
    }

    public function isFile(Path|string $path): bool
    {
        return $this->errorHandler->with(function () use ($path) {
            return is_file($path);
        }, function () {
            return false; // False on E_WARNING
        });
    }

    public function isDirectory(Path|string $path): bool
    {
        return $this->errorHandler->with(function () use ($path) {
            return is_dir($path);
        }, function () {
            return false; // False on E_WARNING
        });
    }

    public function isReadable(Path|string $path): bool
    {
        return $this->errorHandler->with(function () use ($path) {
            return is_readable($path);
        }, function () {
            return false; // False on E_WARNING
        });
    }

    public function isWritable(Path|string $path): bool
    {
        return $this->errorHandler->with(function () use ($path) {
            return is_writable($path);
        }, function () {
            return false; // False on E_WARNING
        });
    }


    /* ---------- Directory methods ---------------------------------------- */

    public function makeDirectory(Path|string $path, int $permissions = 0777, bool $recursive = false): string
    {
        return $this->errorHandler->with(function () use ($path, $permissions, $recursive) {
            mkdir($path, $permissions, $recursive);
            return $this->realPath($path);
        }, new FileSystemException("Could not create directory: '{$path}'"));
    }

    public function removeDirectory(Path|string $path): void
    {
        $this->errorHandler->with(function () use ($path) {
            rmdir($path);
        }, new FileSystemException("Could not remove directory: '{$path}'"));
    }

    public function directory(Path|string $path, bool $create = false): string|null
    {
        if ($this->isDirectory($path)) {
            return $this->realPath($path);
        }
        if ($create) {
            return $this->makeDirectory($path, recursive: true);
        }
        return null;
    }


    /* ---------- Utility methods ------------------------------------------ */

    public function realPath(Path|string $path): string
    {
        $realPath = realpath($path);
        return $realPath === false ? $path : $realPath;
    }
}
