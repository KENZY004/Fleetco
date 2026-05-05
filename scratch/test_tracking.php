<?php
$vehicle = App\Models\Vehicle::first();
$processor = new App\Services\TelemetryProcessor(new App\Services\RiskEngineService());

echo "Odometer Before: " . $vehicle->odometer . "\n";

// Ping 1 (Mumbai Center)
$processor->process($vehicle, [
    'lat' => 19.0760,
    'lng' => 72.8777,
    'speed' => 50,
    'heading' => 0,
    'captured_at' => now()->subMinutes(1)
]);

// Ping 2 (~1.3km away)
$processor->process($vehicle, [
    'lat' => 19.0850,
    'lng' => 72.8850,
    'speed' => 50,
    'heading' => 45,
    'captured_at' => now()
]);

echo "Odometer After: " . $vehicle->fresh()->odometer . "\n";
