<?php

// Conditional post-autoload script runner.
// - RUN_FILAMENT_UPGRADE=true to run `php artisan filament:upgrade`.
// - SKIP_PACKAGE_DISCOVER=true to skip `php artisan package:discover` (not recommended).
// This script ensures CI can opt-out of slow tasks.

$runFilament = getenv('RUN_FILAMENT_UPGRADE') ?: false;
$skipDiscover = getenv('SKIP_PACKAGE_DISCOVER') ?: false;

$cwd = __DIR__ . '/..';
chdir($cwd);

// If SKIP_PACKAGE_DISCOVER is not set, run package discovery (safe, quick).
if (! $skipDiscover) {
    passthru(PHP_BINARY . " artisan package:discover --ansi", $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "package:discover failed with exit code: $exitCode\n");
        exit($exitCode);
    }
}

// Run filament upgrade only when explicitly requested (e.g. not in CI by default)
if ($runFilament) {
    passthru(PHP_BINARY . " artisan filament:upgrade --no-interaction", $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "filament:upgrade failed with exit code: $exitCode\n");
        exit($exitCode);
    }
}

exit(0);
