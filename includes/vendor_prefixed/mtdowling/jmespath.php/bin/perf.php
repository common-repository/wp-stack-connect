#!/usr/bin/env php
<?php 
namespace WPStack_Connect_Vendor;

if (\file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} elseif (\file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} else {
    throw new \RuntimeException('Unable to locate autoload.php file.');
}
$xdebug = new \WPStack_Connect_Vendor\Composer\XdebugHandler\XdebugHandler('perf.php');
$xdebug->check();
unset($xdebug);
$dir = isset($argv[1]) ? $argv[1] : __DIR__ . '/../tests/compliance/perf';
\is_dir($dir) or die('Dir not found: ' . $dir);
// Warm up the runner
\WPStack_Connect_Vendor\JmesPath\Env::search('foo', []);
$total = 0;
foreach (\glob($dir . '/*.json') as $file) {
    $total += \WPStack_Connect_Vendor\runSuite($file);
}
echo "\nTotal time: {$total}\n";
function runSuite($file)
{
    $contents = \file_get_contents($file);
    $json = \json_decode($contents, \true);
    $total = 0;
    foreach ($json as $suite) {
        foreach ($suite['cases'] as $case) {
            $total += \WPStack_Connect_Vendor\runCase($suite['given'], $case['expression'], $case['name']);
        }
    }
    return $total;
}
function runCase($given, $expression, $name)
{
    $best = 99999;
    $runtime = \WPStack_Connect_Vendor\JmesPath\Env::createRuntime();
    for ($i = 0; $i < 100; $i++) {
        $t = \microtime(\true);
        $runtime($expression, $given);
        $tryTime = (\microtime(\true) - $t) * 1000;
        if ($tryTime < $best) {
            $best = $tryTime;
        }
        if (!\getenv('CACHE')) {
            $runtime = \WPStack_Connect_Vendor\JmesPath\Env::createRuntime();
            // Delete compiled scripts if not caching.
            if ($runtime instanceof \WPStack_Connect_Vendor\JmesPath\CompilerRuntime) {
                \array_map('unlink', \glob(\sys_get_temp_dir() . '/jmespath_*.php'));
            }
        }
    }
    \printf("time: %07.4fms name: %s\n", $best, $name);
    return $best;
}
