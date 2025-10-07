<?php

// app/bootstrap.php
declare(strict_types=1);

namespace App;

session_start();

// Load .env (robust, PHPStan-friendly)
$envPath = dirname(__DIR__) . '/.env';

if (is_file($envPath)) {
    $contents = file_get_contents($envPath);
    if ($contents !== false) {
        $lines = preg_split('/\R/', $contents); // split on any newline
        if (is_array($lines)) {
            foreach ($lines as $lineRaw) {
                $line = trim((string) $lineRaw);
                if ($line === '' || (isset($line[0]) && $line[0] === '#')) {
                    continue;
                }

                $pos = strpos($line, '=');
                if ($pos === false) {
                    continue; // malformed line
                }

                $k = trim(substr($line, 0, $pos));
                if ($k === '') {
                    continue; // empty key
                }

                $v = trim(substr($line, $pos + 1));

                // Only set if not already present in environment
                if (getenv($k) === false) {
                    putenv($k . '=' . $v);
                }
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
