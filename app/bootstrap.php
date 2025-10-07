<?php

// app/bootstrap.php
declare(strict_types=1);

namespace App;

session_start();

// Load .env (simple, explicit loader)
$envPath = dirname(__DIR__) . '/.env';
if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $raw) {
            $line = trim($raw);
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false || $pos === 0) {
                // no '=' or empty key → skip
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            // only set if not already present
            if ($key !== '' && getenv($key) === false) {
                putenv($key . '=' . $val);
            }
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
