<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    Schema::create('telematics_logs_test', function (Blueprint $table) {
        $table->id();
        $table->geography('location', subtype: 'point', srid: 4326);
    });
    echo "Success!\n";
    Schema::drop('telematics_logs_test');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
