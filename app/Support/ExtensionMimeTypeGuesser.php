<?php

namespace App\Support;

use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Fallback when php_fileinfo is disabled on the host (common on shared hosting).
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
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === '') {
            return null;
        }

        return self::MAP[$extension] ?? 'application/octet-stream';
    }
}
