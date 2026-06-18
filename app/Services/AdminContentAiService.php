<?php

namespace App\Services;

use RuntimeException;

class AdminContentAiService
{
    public function __construct(
        protected OpenAiClient $openAi,
    ) {}

    public function isAvailable(): bool
    {
        return $this->openAi->isConfigured();
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{description_en: string, description_es: string}
     */
    public function generateDescriptions(string $type, array $context): array
    {
        $this->ensureConfigured();

        $result = $this->openAi->chatJson([
            [
                'role' => 'system',
                'content' => 'You write SEO-friendly rental listing copy for MV Miami Rental (Miami, Florida). '
                    .'Return valid JSON only. Use plain text paragraphs (no HTML). '
                    .'English must sound natural for US search. Spanish must be neutral Latin American Spanish.',
            ],
            [
                'role' => 'user',
                'content' => $this->descriptionPrompt($type, $context),
            ],
        ]);

        if (! $result) {
            throw new RuntimeException('AI could not generate descriptions. Please try again.');
        }

        return [
            'description_en' => trim((string) ($result['description_en'] ?? '')),
            'description_es' => trim((string) ($result['description_es'] ?? '')),
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, string|null>
     */
    public function generateSeo(string $type, array $context): array
    {
        $this->ensureConfigured();

        $result = $this->openAi->chatJson([
            [
                'role' => 'system',
                'content' => 'You are an SEO specialist for MV Miami Rental. Return valid JSON only. '
                    .'Meta title max 60 chars. Meta description max 155 chars. OG description max 200 chars. '
                    .'Keywords: comma-separated, 5-10 phrases. Do not include og_image.',
            ],
            [
                'role' => 'user',
                'content' => $this->seoPrompt($type, $context),
            ],
        ]);

        if (! $result) {
            throw new RuntimeException('AI could not generate SEO fields. Please try again.');
        }

        return [
            'meta_title' => $this->clip($result['meta_title'] ?? '', 255),
            'meta_description' => $this->clip($result['meta_description'] ?? '', 320),
            'meta_keywords' => $this->clip($result['meta_keywords'] ?? '', 255),
            'og_title' => $this->clip($result['og_title'] ?? '', 255),
            'og_description' => $this->clip($result['og_description'] ?? '', 320),
        ];
    }

    protected function ensureConfigured(): void
    {
        if (! $this->openAi->isConfigured()) {
            throw new RuntimeException('OpenAI is not configured. Add OPENAI_API_KEY to your .env file.');
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function descriptionPrompt(string $type, array $context): string
    {
        $facts = $this->formatContext($type, $context);

        $length = match ($type) {
            'blog' => '120-200 words per language',
            default => '80-130 words per language',
        };

        return <<<PROMPT
Write SEO-friendly listing descriptions for a {$this->typeLabel($type)} on MV Miami Rental.

Facts:
{$facts}

Requirements:
- {$length}
- Mention Miami / South Florida naturally where relevant
- Highlight key features, comfort, and why renters should book
- Include subtle calls to action
- No bullet lists unless essential; prefer flowing paragraphs
- Do not invent specs not listed in facts

Return JSON:
{
  "description_en": "...",
  "description_es": "..."
}
PROMPT;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function seoPrompt(string $type, array $context): string
    {
        $facts = $this->formatContext($type, $context);

        return <<<PROMPT
Generate SEO metadata for this {$this->typeLabel($type)} listing on MV Miami Rental.

Facts:
{$facts}

Target: organic search for Miami rentals (cars, homes, or blog readers as appropriate).

Return JSON:
{
  "meta_title": "...",
  "meta_description": "...",
  "meta_keywords": "keyword1, keyword2, ...",
  "og_title": "...",
  "og_description": "..."
}
PROMPT;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function formatContext(string $type, array $context): string
    {
        $lines = [];

        foreach ($context as $key => $value) {
            if ($value === null || $value === '' || (is_array($value) && $value === [])) {
                continue;
            }

            if (is_array($value)) {
                $value = implode(', ', array_map('strval', $value));
            }

            $lines[] = ucfirst(str_replace('_', ' ', (string) $key)).': '.trim((string) $value);
        }

        return $lines ? implode("\n", $lines) : 'No details provided yet.';
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'vehicle' => 'vehicle rental',
            'property' => 'home or apartment rental',
            'blog' => 'blog article',
            default => 'listing',
        };
    }

    protected function clip(mixed $value, int $max): ?string
    {
        $text = trim(strip_tags((string) $value));

        return $text === '' ? null : mb_substr($text, 0, $max);
    }
}
