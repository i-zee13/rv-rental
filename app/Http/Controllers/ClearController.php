<?php

namespace App\Http\Controllers;

use App\Support\StorageLinker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ClearController extends Controller
{
    public function __invoke(Request $request)
    {
        $commands = ['optimize:clear'];

        if (! $request->boolean('no_cache')) {
            $commands = array_merge($commands, [
                'config:cache',
                'route:cache',
                'view:cache',
                'event:cache',
            ]);
        }

        $results = [];

        foreach ($commands as $command) {
            $exitCode = Artisan::call($command);
            $results[$command] = [
                'exit_code' => $exitCode,
                'output' => trim(Artisan::output()),
            ];
        }

        if ($request->boolean('storage', true)) {
            $results['storage:link'] = StorageLinker::ensure();
        }

        return response()->json([
            'status' => 'ok',
            'commands' => $results,
        ]);
    }
}