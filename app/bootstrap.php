<?php
// app/bootstrap.php
declare(strict_types=1);

namespace App;

session_start();

// Load .env (simple loader)
$envPath = dirname(__DIR__) . '/.env';
if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2) + [null, null]);
        if ($k !== null && $v !== null && getenv($k) === false) {
            putenv("$k=$v");
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
