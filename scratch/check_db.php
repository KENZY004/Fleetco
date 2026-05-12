<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Database Facade: " . DB::connection()->getDatabaseName() . "\n";
echo "ENV DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "Has Table 'fleet_routes': " . (Schema::hasTable('fleet_routes') ? 'YES' : 'NO') . "\n";

$searchPath = DB::select("SHOW search_path");
print_r($searchPath);

$migrations = DB::table('migrations')->get();
print_r($migrations);
