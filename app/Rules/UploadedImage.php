<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Validates uploads without relying on php_fileinfo / Symfony MIME guessers.
 */
class UploadedImage implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The :attribute must be a valid uploaded image file.');

            return;
        }

        $ext = strtolower($value->getClientOriginalExtension());
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowedExt, true)) {
            return;
        }

        $clientMime = strtolower((string) $value->getClientMimeType());
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($clientMime, $allowedMime, true)) {
            return;
        }

        $path = $value->getRealPath();
        if ($path && is_readable($path) && $this->hasImageMagicBytes((string) @file_get_contents($path, false, null, 0, 12))) {
            return;
        }

        $fail('The :attribute must be an image (jpg, png, gif, or webp).');
    }

    private function hasImageMagicBytes(string $header): bool
    {
        if ($header === '') {
            return false;
        }

        return str_starts_with($header, "\xFF\xD8\xFF")
            || str_starts_with($header, "\x89PNG\r\n\x1a\n")
            || str_starts_with($header, 'GIF87a')
            || str_starts_with($header, 'GIF89a')
            || (str_starts_with($header, 'RIFF') && strlen($header) >= 12 && substr($header, 8, 4) === 'WEBP');
    }
}
