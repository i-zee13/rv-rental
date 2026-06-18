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
            'note' => $command === 'migrate:fresh'
                ? 'WARNING: migrate:fresh deleted all tables. Do not use refresh=1 on live.'
                : 'Safe: only runs new migrations. Your existing rows are kept. For categories use /seed?class=VehicleCategoriesSeeder — not /migrate?seed=1.',
        ], $exitCode === 0 ? 200 : 500);
    }
}
