<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

use function App\db;
use function App\input_json;
use function App\json_response;

class Auth
{
    public static function register(): void
    {
        $in = input_json();

        foreach (['email', 'password', 'role'] as $k) {
            if (!isset($in[$k])) {
                json_response(['error' => "Missing {$k}"], 422);
            }
        }

        $email = strtolower(trim($in['email']));
        $role  = in_array($in['role'], ['seeker', 'employer'], true) ? $in['role'] : 'seeker';
        $hash  = password_hash($in['password'], PASSWORD_DEFAULT);

        $pdo  = db();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            json_response(['error' => 'Email already in use'], 409);
        }

        $pdo->prepare('INSERT INTO users (email, password_hash, role, name, created_at) VALUES (?, ?, ?, ?, NOW())')
            ->execute([$email, $hash, $role, $in['name'] ?? '']);

        json_response(['ok' => true]);
    }

    public static function login(): void
    {
        $in = input_json();

        foreach (['email', 'password'] as $k) {
            if (!isset($in[$k])) {
                json_response(['error' => "Missing {$k}"], 422);
            }
        }

        $email = strtolower(trim($in['email']));
        $pdo   = db();

        $stmt = $pdo->prepare('SELECT id, email, password_hash, role, name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u || !password_verify($in['password'], $u['password_hash'])) {
            json_response(['error' => 'Invalid credentials'], 401);
        }

        $_SESSION['user'] = [
            'id'    => $u['id'],
            'email' => $u['email'],
            'role'  => $u['role'],
            'name'  => $u['name'],
        ];

        json_response(['ok' => true, 'user' => $_SESSION['user']]);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        json_response(['ok' => true]);
    }

    public static function oauthGoogleStart(): void
    {
        // Stub: In real app, redirect to Google's OAuth 2 consent screen.
        json_response(['todo' => 'Implement Google OAuth using a library (e.g., league/oauth2-client).']);
    }

    public static function oauthLinkedinStart(): void
    {
        // Stub: In real app, redirect to LinkedIn OAuth 2.
        json_response(['todo' => 'Implement LinkedIn OAuth 2 flow.']);
    }
}
