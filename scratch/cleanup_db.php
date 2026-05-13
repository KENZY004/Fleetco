<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting manual cleanup..." . PHP_EOL;

$tables = [
    'maintenance_records',
    'fleet_routes',
    'telematics_logs',
    'driver_telemetry',
    'driver_duty_logs',
    'driver_invitations',
    'risk_events',
    'trips',
    'drivers',
    'vehicles',
    'fleets',
    'users',
    'cache',
    'cache_locks',
    'jobs',
    'job_batches',
    'failed_jobs',
    'sessions',
    'settings',
    'landmarks',
    'password_reset_tokens',
    'migrations'
];

DB::statement('SET session_replication_role = "replica";');

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Dropping table: $table" . PHP_EOL;
        DB::statement("DROP TABLE IF EXISTS \"$table\" CASCADE");
    }
}

DB::statement('SET session_replication_role = "origin";');

echo "Cleanup finished. You can now run 'php artisan migrate --seed'" . PHP_EOL;
