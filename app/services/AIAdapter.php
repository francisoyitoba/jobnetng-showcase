<?php

declare(strict_types=1);

namespace App\Services;

class AIAdapter
{
    public static function analyzeCv(string $text): array
    {
        $provider = getenv('AI_PROVIDER') ?: 'mock';

        if ($provider === 'mock') {
            return [
                'score'    => 78,
                'feedback' => [
                    'Great experience summary',
                    'Consider quantifying achievements',
                    'Add more ATS keywords for your target role',
                ],
            ];
        }

        if ($provider === 'openai') {
            // TODO: call OpenAI API (omitted in showcase)
            return ['score' => 0, 'feedback' => ['OpenAI not configured']];
        }

        return ['score' => 0, 'feedback' => ['AI provider not configured']];
    }

    public static function recommendJobs(array $profile): array
    {
        // Mock recommendations; plug your real logic
        return [
            ['job_id' => 1, 'score' => 0.91, 'reason' => 'Matches PHP + WordPress + MySQL'],
            ['job_id' => 2, 'score' => 0.84, 'reason' => 'Frontend skills align with JD keywords'],
        ];
    }

    public static function writeCoverLetter(array $job, array $profile): string
    {
        $title = $job['title'] ?? 'role';

        return "Dear Hiring Manager,\n\n"
            . "I’m excited to apply for the {$title}. "
            . "My background in PHP, MySQL, and modern frontend aligns with your needs...";
    }
}
