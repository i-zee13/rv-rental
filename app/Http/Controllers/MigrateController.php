<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MigrateController extends Controller
{
    public function __invoke(Request $request)
    {
        $options = ['--force' => true];

        if ($request->boolean('seed')) {
            $options['--seed'] = true;
        }

        $command = $request->boolean('refresh') ? 'migrate:refresh' : 'migrate';
        $exitCode = Artisan::call($command, $options);

        return response()->json([
            'status' => $exitCode === 0 ? 'ok' : 'error',
            'command' => $command,
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output()),
        ], $exitCode === 0 ? 200 : 500);
    }
}
