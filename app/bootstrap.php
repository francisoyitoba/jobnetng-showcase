<?php

// app/bootstrap.php
declare(strict_types=1);

namespace App;

session_start();

// Load .env (simple, explicit loader)
$envPath = dirname(__DIR__) . '/.env';
if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $k = trim(substr($line, 0, $pos));
        $v = trim(substr($line, $pos + 1));

        if ($k === '') {
            continue;
        }

        if (getenv($k) === false) {
            putenv(sprintf('%s=%s', $k, $v));
        }
    }
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/services/Auth.php';
require_once __DIR__ . '/services/AIAdapter.php';
require_once __DIR__ . '/services/Recommendation.php';

// Include routes
require_once dirname(__DIR__) . '/routes/web.php';
