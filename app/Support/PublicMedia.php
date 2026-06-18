<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PublicMedia
{
    public static function store(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        // Store short relative path — full URLs were truncated at 191 chars in DB.
        return '/storage/'.ltrim($path, '/');
    }

    public static function url(string $path): string
    {
        if ($path === '') {
            return '';
        }

        $path = self::normalizeStoredPath($path);

        if (! self::diskPathExists($path)) {
            $recovered = self::findByFilenamePrefix($path);
            if ($recovered !== null) {
                $path = $recovered;
            }
        }

        if (str_starts_with($path, '/storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public static function deleteByUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $relative = self::relativePath(self::normalizeStoredPath($url));

        if ($relative !== null) {
            Storage::disk('public')->delete($relative);
        }
    }

    public static function relativePath(string $url): ?string
    {
        $url = self::normalizeStoredPath($url);

        if (str_starts_with($url, '/storage/')) {
            return ltrim(substr($url, strlen('/storage/')), '/');
        }

        $publicBase = rtrim(Storage::disk('public')->url(''), '/');

        if ($publicBase !== '' && str_starts_with($url, $publicBase.'/')) {
            return ltrim(substr($url, strlen($publicBase) + 1), '/');
        }

        if (! str_starts_with($url, 'http')) {
            return ltrim($url, '/');
        }

        return null;
    }

    public static function normalizeStoredPath(string $path): string
    {
        if (preg_match('#(/storage/.+)$#', $path, $matches)) {
            return $matches[1];
        }

        if (! str_starts_with($path, 'http') && ! str_starts_with($path, '/storage/')) {
            return '/storage/'.ltrim($path, '/');
        }

        return $path;
    }

    protected static function diskPathExists(string $path): bool
    {
        $relative = self::relativePath($path);

        return $relative !== null && Storage::disk('public')->exists($relative);
    }

    /** Recover files when DB path was truncated (e.g. ends in .p instead of .png). */
    protected static function findByFilenamePrefix(string $path): ?string
    {
        $relative = self::relativePath($path);

        if ($relative === null) {
            return null;
        }

        $dir = dirname($relative);
        $filename = basename($relative);

        if ($dir === '.' || $dir === '') {
            return null;
        }

        if (! Storage::disk('public')->exists($dir)) {
            return null;
        }

        $prefix = pathinfo($filename, PATHINFO_FILENAME);

        foreach (Storage::disk('public')->files($dir) as $file) {
            if (str_starts_with(basename($file), $prefix)) {
                return '/storage/'.$file;
            }
        }

        return null;
    }
}
