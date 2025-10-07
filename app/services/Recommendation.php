<?php

declare(strict_types=1);

namespace App\Services;

use function App\db;

class Recommendation
{
    public static function forSeeker(int $userId): array
    {
        $pdo  = db();
        $stmt = $pdo->query(
            "SELECT id, title, location, category
             FROM jobs
             WHERE status = 'published'
             ORDER BY created_at DESC
             LIMIT 10"
        );

        return $stmt->fetchAll() ?: [];
    }
}
