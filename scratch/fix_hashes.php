<?php

use App\Models\Vehicle;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vehicles = Vehicle::all();
foreach ($vehicles as $vehicle) {
    if (empty($vehicle->tracking_hash)) {
        $vehicle->tracking_hash = Str::random(16);
        $vehicle->save();
        echo "Updated vehicle: {$vehicle->name} with hash: {$vehicle->tracking_hash}\n";
    }
}
