<?php

// app/services/AIAdapter.php
declare(strict_types=1);

namespace App\Services;

use function App\json_response;

class AIAdapter
{
    public static function analyzeCv(string $text): array
    {
        $provider = getenv('AI_PROVIDER') ?: 'mock';
        if ($provider === 'mock') {
            return [
                'score' => 78,
                'feedback' => ['Great experience summary','Consider quantifying achievements','Add more ATS keywords for your target role']
            ];
        }
        if ($provider === 'openai') {
            // TODO: call OpenAI API with your prompt & OPENAI_API_KEY (omitted in showcase)
            return ['score' => 0,'feedback' => ['OpenAI not configured']];
        }
        return ['score' => 0,'feedback' => ['AI provider not configured']];
    }

    public static function recommendJobs(array $profile): array
    {
        // Mock recommendations; plug your real logic (vector search, skill match, etc.)
        return [
            ['job_id' => 1,'score' => 0.91,'reason' => 'Matches PHP + WordPress + MySQL'],
            ['job_id' => 2,'score' => 0.84,'reason' => 'Frontend skills align with JD keywords'],
        ];
    }

    public static function writeCoverLetter(array $job, array $profile): string
    {
        return "Dear Hiring Manager,\n\nI’m excited to apply for the " . ($job['title'] ?? 'role') .
               ". My background in PHP, MySQL, and modern frontend aligns with your needs..." ;
    }
}
