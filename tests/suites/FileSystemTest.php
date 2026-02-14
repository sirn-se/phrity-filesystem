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

class FileSystemTest extends TestCase
{
    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
        ini_set('open_basedir', __DIR__ . '/../../:' . sys_get_temp_dir());
    }

    public function testExists(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->exists("{$bp}/empty-file"));
        $this->assertTrue($fs->exists("{$bp}/empty-dir"));
        $this->assertTrue($fs->exists("{$bp}/symlink-file"));
        $this->assertTrue($fs->exists("{$bp}/symlink-dir"));
        $this->assertTrue($fs->exists("{$bp}/readonly-file"));
        $this->assertFalse($fs->exists("{$bp}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->exists('/'));
    }

    public function testIsFile(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isFile("{$bp}/empty-file"));
        $this->assertFalse($fs->isFile("{$bp}/empty-dir"));
        $this->assertTrue($fs->isFile("{$bp}/symlink-file"));
        $this->assertFalse($fs->isFile("{$bp}/symlink-dir"));
        $this->assertTrue($fs->isFile("{$bp}/readonly-file"));
        $this->assertFalse($fs->isFile("{$bp}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isFile('/'));
    }

    public function testIsDirectory(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        // Absolute paths
        $this->assertFalse($fs->isDirectory("{$bp}/empty-file"));
        $this->assertTrue($fs->isDirectory("{$bp}/empty-dir"));
        $this->assertFalse($fs->isDirectory("{$bp}/symlink-file"));
        $this->assertTrue($fs->isDirectory("{$bp}/symlink-dir"));
        $this->assertFalse($fs->isDirectory("{$bp}/readonly-file"));
        $this->assertFalse($fs->isDirectory("{$bp}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isDirectory('/'));
    }

    public function testIsReadable(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isReadable("{$bp}/empty-file"));
        $this->assertTrue($fs->isReadable("{$bp}/empty-dir"));
        $this->assertTrue($fs->isReadable("{$bp}/symlink-file"));
        $this->assertTrue($fs->isReadable("{$bp}/symlink-dir"));
        $this->assertTrue($fs->isReadable("{$bp}/readonly-file"));
        $this->assertFalse($fs->isReadable("{$bp}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isReadable('/'));
    }

    public function testIsWritable(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isWritable("{$bp}/empty-file"));
        $this->assertTrue($fs->isWritable("{$bp}/empty-dir"));
        $this->assertTrue($fs->isWritable("{$bp}/symlink-file"));
        $this->assertTrue($fs->isWritable("{$bp}/symlink-dir"));
        $this->assertFalse($fs->isWritable("{$bp}/readonly-file"));
        $this->assertFalse($fs->isWritable("{$bp}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isWritable('/'));
    }

    public function testMakeDirectory(): void
    {
        $bp = sys_get_temp_dir();
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$bp}/tmp-directory-" . rand(10000, 99999));
        $this->assertTrue($fs->isDirectory($dirPath));
        $fs->removeDirectory($dirPath);
        $this->assertFalse($fs->isDirectory($dirPath));
    }

    public function testMakeDirectoryRecursive(): void
    {
        $bp = sys_get_temp_dir();
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$bp}/tmp-directory/dir/dir-" . rand(10000, 99999), recursive: true);
        $this->assertTrue($fs->isDirectory($dirPath));
        $fs->removeDirectory($dirPath);
        $this->assertFalse($fs->isDirectory($dirPath));
    }

    public function testMakeDirectoryExistException(): void
    {
        $bp = sys_get_temp_dir();
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$bp}/tmp-directory-" . rand(10000, 99999));
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not create directory:');
        $dirPath = $fs->makeDirectory($dirPath);
    }

    public function testMakeDirectoryRecursiveException(): void
    {
        $bp = sys_get_temp_dir();
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not create directory:');
        $fs->makeDirectory("{$bp}/tmp-directory/fail/dir-" . rand(10000, 99999));
    }

    public function testRemoveDirectoryNonExistingException(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not remove directory:');
        $fs->removeDirectory("{$bp}/non-existing");
    }

    public function testRemoveDirectoryNonEmptyException(): void
    {
        $bp = __DIR__ . '/../fixtures';
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not remove directory:');
        $fs->removeDirectory("{$bp}");
    }

    public function testDirectory(): void
    {
        $bp = sys_get_temp_dir();
        $dir = "{$bp}/tmp-directory/dir/dir-" . rand(10000, 99999);
        $fs = new FileSystem();
        // Not existing: null
        $dirPath = $fs->directory($dir);
        $this->assertNull($dirPath);
        // Created (recursive)
        $dirPath = $fs->directory($dir, true);
        $this->assertIsString($dirPath);
        $this->assertTrue($fs->isDirectory($dirPath));
        // Existing (must not fail)
        $dirPath = $fs->directory($dir, true);
        $this->assertIsString($dirPath);
        $this->assertTrue($fs->isDirectory($dirPath));
        $fs->removeDirectory($dirPath);
        $this->assertFalse($fs->isDirectory($dirPath));
    }
}
