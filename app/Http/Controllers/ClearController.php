<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ClearController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! app()->environment('local') && $request->query('key') !== config('app.clear_key')) {
            abort(404);
        }

        $commands = [
            'optimize:clear',
            'config:cache',
            'route:cache',
            'view:cache',
            'event:cache',
        ];

        $results = [];

        foreach ($commands as $command) {
            $exitCode = Artisan::call($command);
            $results[$command] = [
                'exit_code' => $exitCode,
                'output' => trim(Artisan::output()),
            ];
        }

        return response()->json([
            'status' => 'ok',
            'commands' => $results,
        ]);
    }
}
