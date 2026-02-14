<?php

/**
 * File for ErrorHandler function tests.
 * @package Phrity > Util > ErrorHandler
 */

declare(strict_types=1);

namespace Phrity\FileSystem;

use ErrorException;
use RuntimeException;
use Phrity\Util\ErrorHandler;
use PHPUnit\Framework\TestCase;

/**
 * ErrorHandler test class.
 */
class PathTest extends TestCase
{
    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
    }

    public function testPathString(): void
    {
        $path = new Path('/test/path');
        $this->assertEquals('/test/path', (string)$path);
    }

    public function testPathPath(): void
    {
        $path = new Path(new Path('/test/path'));
        $this->assertEquals('/test/path', (string)$path);
    }
}
