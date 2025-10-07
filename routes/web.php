<?php

declare(strict_types=1);

use App\Services\AIAdapter;
use App\Services\Auth;
use App\Services\Recommendation;

use function App\auth_required;
use function App\db;
use function App\input_json;
use function App\json_response;

$GLOBALS['ROUTES'] = [
    // Health
    ['GET', '~^/api/health$~', function (): void {
        json_response(['ok' => true, 'ts' => time()]);
    }],

    // Auth
    ['POST', '~^/api/auth/register$~', function (): void {
        Auth::register();
    }],
    ['POST', '~^/api/auth/login$~', function (): void {
        Auth::login();
    }],
    ['POST', '~^/api/auth/logout$~', function (): void {
        Auth::logout();
    }],

    // OAuth stubs (camelCase method names)
    ['GET', '~^/api/oauth/google$~', function (): void {
        Auth::oauthGoogleStart();
    }],
    ['GET', '~^/api/oauth/linkedin$~', function (): void {
        Auth::oauthLinkedinStart();
    }],

    // Jobs (public) with pagination
    ['GET', '~^/api/jobs$~', function (): void {
        $pdo = db();

        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $limit  = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;

        $stmt = $pdo->prepare(
            'SELECT SQL_CALC_FOUND_ROWS
                    id, title, description, location, category, created_at
             FROM jobs
             WHERE status = \'published\'
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows  = $stmt->fetchAll();
        $total = (int) $pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
        $pages = (int) ceil($total / $limit);

        json_response([
            'jobs' => $rows,
            'meta' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => $pages,
            ],
        ]);
    }],

    // Apply to a job (seeker)
    ['POST', '~^/api/jobs/apply$~', function (): void {
        $user = auth_required();

        if ($user['role'] !== 'seeker') {
            json_response(['error' => 'Forbidden'], 403);
        }

        $in = input_json();
        foreach (['job_id'] as $k) {
            if (!isset($in[$k])) {
                json_response(['error' => "Missing {$k}"], 422);
            }
        }

        $pdo   = db();
        $stmt  = $pdo->prepare(
            'INSERT INTO job_apps (job_id, seeker_user_id, status, cover_letter_text, created_at)
             VALUES (?, ?, \'pending\', ?, NOW())'
        );
        $cover = $in['cover_letter'] ?? '';
        $stmt->execute([$in['job_id'], $user['id'], $cover]);

        json_response(['ok' => true]);
    }],

    // My applications (seeker)
    ['GET', '~^/api/applications/mine$~', function (): void {
        $user = auth_required();

        if ($user['role'] !== 'seeker') {
            json_response(['error' => 'Forbidden'], 403);
        }

        $pdo  = db();
        $stmt = $pdo->prepare(
            'SELECT a.id, a.job_id, a.status, j.title
             FROM job_apps a
             JOIN jobs j ON j.id = a.job_id
             WHERE a.seeker_user_id = ?
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$user['id']]);

        json_response(['applications' => $stmt->fetchAll()]);
    }],

    // Employer: create job
    ['POST', '~^/api/jobs/create$~', function (): void {
        $user = auth_required();

        if ($user['role'] !== 'employer') {
            json_response(['error' => 'Forbidden'], 403);
        }

        $in = input_json();
        foreach (['title', 'description', 'location', 'category'] as $k) {
            if (!isset($in[$k])) {
                json_response(['error' => "Missing {$k}"], 422);
            }
        }

        $pdo  = db();
        $stmt = $pdo->prepare(
            'INSERT INTO jobs (employer_user_id, title, description, location, category, status, created_at)
             VALUES (?, ?, ?, ?, ?, \'published\', NOW())'
        );
        $stmt->execute([$user['id'], $in['title'], $in['description'], $in['location'], $in['category']]);

        json_response(['ok' => true]);
    }],

    // AI endpoints (showcase)
    ['POST', '~^/api/ai/cv/analyze$~', function (): void {
        $user = auth_required();

        $in   = input_json();
        $text = $in['text'] ?? '';

        json_response(AIAdapter::analyzeCv($text));
    }],

    ['POST', '~^/api/ai/cover-letter$~', function (): void {
        $user = auth_required();

        $in      = input_json();
        $job     = $in['job'] ?? ['title' => 'Software Developer'];
        $profile = ['name' => $user['name'] ?? 'Candidate'];

        json_response(['cover_letter' => AIAdapter::writeCoverLetter($job, $profile)]);
    }],

    ['GET', '~^/api/ai/recommendations$~', function (): void {
        $user = auth_required();

        if ($user['role'] !== 'seeker') {
            json_response(['error' => 'Forbidden'], 403);
        }

        json_response(['recommendations' => Recommendation::forSeeker((int) $user['id'])]);
    }],
];
