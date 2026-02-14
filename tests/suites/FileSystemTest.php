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
    private string $testDir = '';

    public function setUp(): void
    {
        error_reporting(-1);
        $this->testDir = sys_get_temp_dir() . '/phrity-filesystem';
        ini_set('open_basedir', __DIR__ . '/../../:' . $this->testDir);
    }

    public function testExists(): void
    {
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->exists("{$this->testDir}/empty-file"));
        $this->assertTrue($fs->exists("{$this->testDir}/empty-dir"));
        $this->assertTrue($fs->exists("{$this->testDir}/symlink-file"));
        $this->assertTrue($fs->exists("{$this->testDir}/symlink-dir"));
        $this->assertTrue($fs->exists("{$this->testDir}/readonly-file"));
        $this->assertTrue($fs->exists("{$this->testDir}/writeonly-file"));
        $this->assertFalse($fs->exists("{$this->testDir}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->exists('/'));
    }

    public function testIsFile(): void
    {
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isFile("{$this->testDir}/empty-file"));
        $this->assertFalse($fs->isFile("{$this->testDir}/empty-dir"));
        $this->assertTrue($fs->isFile("{$this->testDir}/symlink-file"));
        $this->assertFalse($fs->isFile("{$this->testDir}/symlink-dir"));
        $this->assertTrue($fs->isFile("{$this->testDir}/readonly-file"));
        $this->assertTrue($fs->isFile("{$this->testDir}/writeonly-file"));
        $this->assertFalse($fs->isFile("{$this->testDir}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isFile('/'));
    }

    public function testIsDirectory(): void
    {
        $fs = new FileSystem();
        // Absolute paths
        $this->assertFalse($fs->isDirectory("{$this->testDir}/empty-file"));
        $this->assertTrue($fs->isDirectory("{$this->testDir}/empty-dir"));
        $this->assertFalse($fs->isDirectory("{$this->testDir}/symlink-file"));
        $this->assertTrue($fs->isDirectory("{$this->testDir}/symlink-dir"));
        $this->assertFalse($fs->isDirectory("{$this->testDir}/readonly-file"));
        $this->assertFalse($fs->isDirectory("{$this->testDir}/writeonly-file"));
        $this->assertFalse($fs->isDirectory("{$this->testDir}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isDirectory('/'));
    }

    public function testIsReadable(): void
    {
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isReadable("{$this->testDir}/empty-file"));
        $this->assertTrue($fs->isReadable("{$this->testDir}/empty-dir"));
        $this->assertTrue($fs->isReadable("{$this->testDir}/symlink-file"));
        $this->assertTrue($fs->isReadable("{$this->testDir}/symlink-dir"));
        $this->assertTrue($fs->isReadable("{$this->testDir}/readonly-file"));
        $this->assertFalse($fs->isReadable("{$this->testDir}/writeonly-file"));
        $this->assertFalse($fs->isReadable("{$this->testDir}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isReadable('/'));
    }

    public function testIsWritable(): void
    {
        $fs = new FileSystem();
        // Absolute paths
        $this->assertTrue($fs->isWritable("{$this->testDir}/empty-file"));
        $this->assertTrue($fs->isWritable("{$this->testDir}/empty-dir"));
        $this->assertTrue($fs->isWritable("{$this->testDir}/symlink-file"));
        $this->assertTrue($fs->isWritable("{$this->testDir}/symlink-dir"));
        $this->assertFalse($fs->isWritable("{$this->testDir}/readonly-file"));
        $this->assertTrue($fs->isWritable("{$this->testDir}/writeonly-file"));
        $this->assertFalse($fs->isWritable("{$this->testDir}/non-existing"));
        // Error as outside open_basedir => false
        $this->assertFalse($fs->isWritable('/'));
    }

    public function testMakeDirectory(): void
    {
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$this->testDir}/new-directory");
        $this->assertTrue($fs->isDirectory($dirPath));
        $fs->removeDirectory($dirPath);
        $this->assertFalse($fs->isDirectory($dirPath));
    }

    public function testMakeDirectoryRecursive(): void
    {
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$this->testDir}/recursive/new-directory", recursive: true);
        $this->assertTrue($fs->isDirectory($dirPath));
        $fs->removeDirectory($dirPath);
        $this->assertFalse($fs->isDirectory($dirPath));
    }

    public function testMakeDirectoryExistException(): void
    {
        $fs = new FileSystem();
        $dirPath = $fs->makeDirectory("{$this->testDir}/new-directory");
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not create directory:');
        $dirPath = $fs->makeDirectory($dirPath);
    }

    public function testMakeDirectoryRecursiveException(): void
    {
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not create directory:');
        $fs->makeDirectory("{$this->testDir}/non-existing/new-directory");
    }

    public function testRemoveDirectoryNonExistingException(): void
    {
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not remove directory:');
        $fs->removeDirectory("{$this->testDir}/non-existing");
    }

    public function testRemoveDirectoryNonEmptyException(): void
    {
        $fs = new FileSystem();
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not remove directory:');
        $fs->removeDirectory("{$this->testDir}");
    }

    public function testDirectory(): void
    {
        $dir = "{$this->testDir}/persistent/directory";
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
