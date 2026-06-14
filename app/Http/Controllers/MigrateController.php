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

        // migrate:fresh drops all tables safely (MySQL FK-safe); refresh rolls back one-by-one and often fails on FKs
        $command = $request->boolean('refresh') ? 'migrate:fresh' : 'migrate';
        $exitCode = Artisan::call($command, $options);

        return response()->json([
            'status' => $exitCode === 0 ? 'ok' : 'error',
            'command' => $command,
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output()),
        ], $exitCode === 0 ? 200 : 500);
    }
}
