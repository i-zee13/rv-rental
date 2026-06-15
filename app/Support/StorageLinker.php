<?php

namespace App\Support;

class StorageLinker
{
    public static function ensure(): array
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (! is_dir($target)) {
            @mkdir($target, 0755, true);
        }

        if (is_link($link)) {
            return [
                'exit_code' => 0,
                'output' => 'Storage symlink already exists.',
            ];
        }

        if (file_exists($link)) {
            return [
                'exit_code' => 0,
                'output' => 'public/storage already exists.',
            ];
        }

        if (function_exists('symlink')) {
            try {
                if (@symlink($target, $link)) {
                    return [
                        'exit_code' => 0,
                        'output' => 'Storage symlink created.',
                    ];
                }
            } catch (\Throwable) {
                // Host disabled symlink — route fallback handles /storage/*
            }
        }

        return [
            'exit_code' => 0,
            'output' => 'Symlink not available on this host. Files are served via the /storage route fallback.',
        ];
    }
}
