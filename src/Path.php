<?php

namespace Phrity\FileSystem;

use Stringable;

class Path implements Stringable
{
    private string $path;

    public function __construct(Path|string $path)
    {
        $this->path = (string)$path;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
