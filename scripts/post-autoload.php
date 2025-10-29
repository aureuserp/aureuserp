<?php

// Conditional post-autoload script runner.
// - RUN_FILAMENT_UPGRADE=true to run `php artisan filament:upgrade`.
// - SKIP_PACKAGE_DISCOVER=true to skip `php artisan package:discover` (not recommended).
// This script ensures CI can opt-out of slow tasks.

$runFilament = getenv('RUN_FILAMENT_UPGRADE') ?: false;
$skipDiscover = getenv('SKIP_PACKAGE_DISCOVER') ?: false;

$cwd = __DIR__ . '/..';
chdir($cwd);
// Simple logger helper
function logline(string $msg): void
{
    $time = date('Y-m-d H:i:s');
    fwrite(STDOUT, "[$time] $msg\n");
    // Attempt to flush
    if (ob_get_level()) { ob_flush(); }
    flush();
}

// If SKIP_PACKAGE_DISCOVER is not set, run package discovery (safe, quick).
if (! $skipDiscover) {
    logline('Starting package:discover...');
    passthru(PHP_BINARY . " artisan package:discover --ansi", $exitCode);
    logline('package:discover exit code: ' . $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "package:discover failed with exit code: $exitCode\n");
        exit($exitCode);
    }
} else {
    logline('SKIP_PACKAGE_DISCOVER is set; skipping package discovery.');
}

// Run filament upgrade only when explicitly requested (e.g. not in CI by default)
if ($runFilament) {
    logline('Starting filament:upgrade...');
    passthru(PHP_BINARY . " artisan filament:upgrade --no-interaction", $exitCode);
    logline('filament:upgrade exit code: ' . $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "filament:upgrade failed with exit code: $exitCode\n");
        exit($exitCode);
    }
} else {
    logline('RUN_FILAMENT_UPGRADE not set; skipping filament:upgrade.');
}

logline('post-autoload script completed.');

exit(0);
