<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// Setting up test files
$testDir = sys_get_temp_dir() . '/phrity-filesystem';
if (file_exists($testDir)) {
    shell_exec("rm -rf {$testDir}");
}
mkdir($testDir);
mkdir("{$testDir}/empty-dir");
touch("{$testDir}/empty-file");
touch("{$testDir}/readonly-file");
chmod("{$testDir}/readonly-file", 0444);
touch("{$testDir}/writeonly-file");
chmod("{$testDir}/writeonly-file", 0222);
symlink("{$testDir}/empty-file", "{$testDir}/symlink-file");
symlink("{$testDir}/empty-dir", "{$testDir}/symlink-dir");
