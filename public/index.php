<?php
// public/index.php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use function App\json_response;
use function App\route_request;

try {
    route_request();
} catch (Throwable $e) {
    if ((int) getenv('APP_DEBUG') === 1) {
        json_response(['error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
    } else {
        json_response(['error' => 'Server error'], 500);
    }
}
