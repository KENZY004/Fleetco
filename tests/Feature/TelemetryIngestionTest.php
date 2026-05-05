<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Models\Trip;
use App\Models\RiskEvent;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelemetryIngestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_telemetry_ingestion_creates_log_and_updates_status()
    {
        $vehicle = Vehicle::create([
            'name' => 'Truck 1',
            'license_plate' => 'ABC-123',
            'status' => 'offline',
            'telemetry_token' => 'TEST-TOKEN-123'
        ]);

        $response = $this->postJson('/api/telematics', [
            'token' => 'TEST-TOKEN-123',
            'lat' => 12.9716,
            'lng' => 77.5946,
            'speed' => 60,
            'heading' => 90,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('telematics_logs', [
            'vehicle_id' => $vehicle->id,
            'speed' => 60,
        ]);

        $this->assertEquals('active', $vehicle->fresh()->status);
    }

    public function test_speeding_creates_risk_event()
    {
        $driver = Driver::create(['name' => 'John Doe']);
        $vehicle = Vehicle::create([
            'name' => 'Truck 1',
            'license_plate' => 'ABC-123',
            'current_driver_id' => $driver->id,
            'telemetry_token' => 'SPEED-TOKEN'
        ]);

        $this->postJson('/api/telematics', [
            'token' => 'SPEED-TOKEN',
            'lat' => 12.9716,
            'lng' => 77.5946,
            'speed' => 120, // Over limit
            'heading' => 90,
        ]);

        $this->assertDatabaseHas('risk_events', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'type' => 'speeding',
        ]);
    }

    public function test_invalid_token_returns_401()
    {
        $response = $this->postJson('/api/telematics', [
            'token' => 'INVALID-TOKEN',
            'lat' => 12.9716,
            'lng' => 77.5946,
        ]);

        $response->assertStatus(401);
    }
}
