<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SeedController extends Controller
{
    /** Seeders safe to run on live — add/update only, never wipe user data. */
    private const ALLOWED = [
        'VehicleCategoriesSeeder',
        'PropertyTypesSeeder',
    ];

    public function __invoke(Request $request)
    {
        $class = $request->query('class', 'VehicleCategoriesSeeder');

        if (! in_array($class, self::ALLOWED, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seeder not allowed. Use: '.implode(', ', self::ALLOWED),
            ], 422);
        }

        $exitCode = Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\'.$class,
            '--force' => true,
        ]);

        return response()->json([
            'status' => $exitCode === 0 ? 'ok' : 'error',
            'class' => $class,
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output()),
            'note' => 'Safe for live: only adds or updates default rows. Your vehicles, bookings, and users are not deleted.',
        ], $exitCode === 0 ? 200 : 500);
    }
}
