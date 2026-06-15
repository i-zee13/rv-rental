<?php

namespace App\Support;

use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Fallback when php_fileinfo is disabled or fails on temp upload paths.
 */
class ExtensionMimeTypeGuesser implements MimeTypeGuesserInterface
{
    private const MAP = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'html' => 'text/html',
        'htm' => 'text/html',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];

    public function isGuesserSupported(): bool
    {
        return true;
    }

    public function guessMimeType(string $path): ?string
    {
        if (is_readable($path)) {
            $header = @file_get_contents($path, false, null, 0, 12);

            if ($header !== false) {
                $magic = $this->guessFromMagicBytes($header);

                if ($magic !== null) {
                    return $magic;
                }
            }
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === '') {
            return null;
        }

        return self::MAP[$extension] ?? 'application/octet-stream';
    }

    private function guessFromMagicBytes(string $header): ?string
    {
        if (str_starts_with($header, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        if (str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }

        if (str_starts_with($header, 'GIF87a') || str_starts_with($header, 'GIF89a')) {
            return 'image/gif';
        }

        if (str_starts_with($header, 'RIFF') && strlen($header) >= 12 && substr($header, 8, 4) === 'WEBP') {
            return 'image/webp';
        }

        if (str_starts_with($header, '%PDF')) {
            return 'application/pdf';
        }

        return null;
    }
}
