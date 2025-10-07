<?php

// routes/web.php
declare(strict_types=1);

use function App\json_response;
use function App\input_json;
use function App\auth_required;
use function App\db;

use App\Services\Auth;
use App\Services\AIAdapter;
use App\Services\Recommendation;

$GLOBALS['ROUTES'] = [
    // Health
    ['GET', '~^/api/health$~', function () {
        return json_response(['ok' => true,'ts' => time()]);
    }],

    // Auth
    ['POST', '~^/api/auth/register$~', function () {
        Auth::register();
    }],
    ['POST', '~^/api/auth/login$~',    function () {
        Auth::login();
    }],
    ['POST', '~^/api/auth/logout$~',   function () {
        Auth::logout();
    }],

    // OAuth stubs
    ['GET',  '~^/api/oauth/google$~',   function () {
        Auth::oauth_google_start();
    }],
    ['GET',  '~^/api/oauth/linkedin$~', function () {
        Auth::oauth_linkedin_start();
    }],

    // Jobs (public)
    ['GET', '~^/api/jobs$~', function () {
        $pdo = db();
        $q = $pdo->query("SELECT id,title,description,location,category,created_at FROM jobs WHERE status='published' ORDER BY created_at DESC LIMIT 50");
        return json_response(['jobs' => $q->fetchAll()]);
    }],

    // Apply to a job (seeker)
    ['POST', '~^/api/jobs/apply$~', function () {
        $user = auth_required();
        if ($user['role'] !== 'seeker') {
            return json_response(['error' => 'Forbidden'], 403);
        }
        $in = input_json();
        foreach (['job_id'] as $k) {
            if (!isset($in[$k])) {
                return json_response(['error' => "Missing $k"], 422);
            }
        }

        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO job_apps (job_id,seeker_user_id,status,cover_letter_text,created_at) VALUES (?,?, 'pending', ?, NOW())");
        $cover = $in['cover_letter'] ?? '';
        $stmt->execute([$in['job_id'], $user['id'], $cover]);
        return json_response(['ok' => true]);
    }],

    // My applications (seeker)
    ['GET', '~^/api/applications/mine$~', function () {
        $user = auth_required();
        if ($user['role'] !== 'seeker') {
            return json_response(['error' => 'Forbidden'], 403);
        }
        $pdo = db();
        $stmt = $pdo->prepare("SELECT a.id,a.job_id,a.status,j.title FROM job_apps a JOIN jobs j ON j.id=a.job_id WHERE a.seeker_user_id=? ORDER BY a.created_at DESC");
        $stmt->execute([$user['id']]);
        return json_response(['applications' => $stmt->fetchAll()]);
    }],

    // Employer: create job
    ['POST', '~^/api/jobs/create$~', function () {
        $user = auth_required();
        if ($user['role'] !== 'employer') {
            return json_response(['error' => 'Forbidden'], 403);
        }
        $in = input_json();
        foreach (['title','description','location','category'] as $k) {
            if (!isset($in[$k])) {
                return json_response(['error' => "Missing $k"], 422);
            }
        }

        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO jobs (employer_user_id,title,description,location,category,status,created_at) VALUES (?,?,?,?,?,'published',NOW())");
        $stmt->execute([$user['id'],$in['title'],$in['description'],$in['location'],$in['category']]);
        return json_response(['ok' => true]);
    }],

    // AI endpoints (showcase)
    ['POST', '~^/api/ai/cv/analyze$~', function () {
        $user = auth_required();
        $in = input_json();
        $text = $in['text'] ?? '';
        return json_response(AIAdapter::analyzeCv($text));
    }],

    ['POST', '~^/api/ai/cover-letter$~', function () {
        $user = auth_required();
        $in = input_json();
        $job = $in['job'] ?? ['title' => 'Software Developer'];
        $profile = ['name' => $user['name'] ?? 'Candidate'];
        return json_response(['cover_letter' => AIAdapter::writeCoverLetter($job, $profile)]);
    }],

    ['GET', '~^/api/ai/recommendations$~', function () {
        $user = auth_required();
        if ($user['role'] !== 'seeker') {
            return json_response(['error' => 'Forbidden'], 403);
        }
        return json_response(['recommendations' => Recommendation::forSeeker((int)$user['id'])]);
    }],
];
