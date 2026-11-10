<?php

namespace App\Services;

class LeadSpamGuard
{
    public function analyze(?string $message): array
    {
        $text = trim((string) $message);

        if ($text === '') {
            return ['is_spam' => false, 'score' => 0, 'reasons' => []];
        }

        $lower = mb_strtolower($text);
        $score = 0;
        $reasons = [];

        if ($this->containsUrl($text)) {
            $score += (int) config('leads.spam_url_score', 3);
            $reasons[] = 'url';
        }

        $phraseScore = (int) config('leads.spam_phrase_score', 2);
        foreach (config('leads.spam_phrases', []) as $phrase) {
            $phrase = mb_strtolower(trim((string) $phrase));
            if ($phrase === '') {
                continue;
            }
            if (str_contains($lower, $phrase)) {
                $score += $phraseScore;
                $reasons[] = 'phrase:'.$phrase;
            }
        }

        $threshold = (int) config('leads.spam_score_threshold', 3);

        return [
            'is_spam' => $score >= $threshold,
            'score' => $score,
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    protected function containsUrl(string $text): bool
    {
        if (preg_match('/https?:\/\/|www\./i', $text)) {
            return true;
        }

        // Bare domains with common TLDs (avoids matching "U.S.A", "e.g.")
        return (bool) preg_match(
            '/\b[a-z0-9][a-z0-9\-]{1,}(?:\.[a-z0-9\-]+)*\.(?:com|net|org|io|co|ai|app|xyz|info|biz|us|uk|ca|de|fr|es|me|site|online|agency|cloud|dev)\b/i',
            $text
        );
    }
}
