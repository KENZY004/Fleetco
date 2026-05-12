<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    echo "Starting migration...<br>";
    Artisan::call('migrate', ['--force' => true]);
    echo "Migration output:<br><pre>" . Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
