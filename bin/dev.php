#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Development server launcher with env file support.
 *
 * Usage:
 *   php bin/dev.php [env-file] [host:port]
 *
 * Examples:
 *   php bin/dev.php                            # uses .envs/.env.local, port 8000
 *   php bin/dev.php .envs/.env.pg.local        # uses PostgreSQL env
 *   php bin/dev.php .envs/.env.local 8080      # custom port
 */

$projectRoot = dirname(__DIR__);
$envFile     = $argv[1] ?? $projectRoot . '/.envs/.env.local';
$port        = $argv[2] ?? '8000';

// Load env file if it exists
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            // Strip inline comments
            if (str_contains($value, ' #')) {
                $value = trim(explode(' #', $value, 2)[0]);
            }
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
    echo "[dev] Loaded env: {$envFile}\n";
} else {
    echo "[dev] Warning: env file not found: {$envFile}\n";
    echo "[dev] Copy .envs/.env.example to .envs/.env.local to get started.\n";
}

$host    = 'localhost';
$docroot = $projectRoot . '/public';

echo "[dev] Starting server: http://{$host}:{$port}\n";
echo "[dev] DB_CONNECTION = " . (getenv('DB_CONNECTION') ?: 'sqlite (default)') . "\n";
echo "[dev] APP_ENV       = " . (getenv('APP_ENV') ?: 'development (default)') . "\n\n";

// Execute the PHP built-in server
$cmd = sprintf(
    'php -S %s:%s -t %s',
    escapeshellarg($host),
    escapeshellarg((string)$port),
    escapeshellarg($docroot)
);

passthru($cmd);
