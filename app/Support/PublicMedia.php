<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PublicMedia
{
    public static function store(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        return self::url($path);
    }

    public static function url(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public static function deleteByUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $relative = self::relativePath($url);

        if ($relative !== null) {
            Storage::disk('public')->delete($relative);
        }
    }

    public static function relativePath(string $url): ?string
    {
        if (str_starts_with($url, '/storage/')) {
            return ltrim(substr($url, strlen('/storage/')), '/');
        }

        $publicBase = rtrim(Storage::disk('public')->url(''), '/');

        if ($publicBase !== '' && str_starts_with($url, $publicBase.'/')) {
            return ltrim(substr($url, strlen($publicBase) + 1), '/');
        }

        return null;
    }
}
