<?php

// app/helpers.php
declare(strict_types=1);

namespace App;

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_fields(array $data, array $keys): void
{
    foreach ($keys as $k) {
        if (!array_key_exists($k, $data)) {
            json_response(['error' => "Missing field: $k"], 422);
        }
    }
}

function auth_required(): array
{
    if (!isset($_SESSION['user'])) {
        json_response(['error' => 'Unauthorized'], 401);
    }
    return $_SESSION['user'];
}

function route_request(): void
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $routes = $GLOBALS['ROUTES'] ?? [];

    foreach ($routes as $r) {
        [$rm, $pattern, $handler] = $r;
        if ($rm !== $method) {
            continue;
        }
        if (preg_match($pattern, $path, $m)) {
            array_shift($m);
            echo $handler(...$m);
            return;
        }
    }
    json_response(['error' => 'Not Found', 'path' => $path], 404);
}
