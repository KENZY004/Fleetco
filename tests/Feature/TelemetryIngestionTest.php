<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Models\Trip;
use App\Models\RiskEvent;
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
        ]);

        $response = $this->postJson('/api/telematics', [
            'license_plate' => 'ABC-123',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'speed' => 60,
            'heading' => 90,
            'secret' => 'fleetco_secret_2024',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'TELEMETRY_INGESTED');

        $this->assertDatabaseHas('telematics_logs', [
            'vehicle_id' => $vehicle->id,
            'speed' => 60,
        ]);

        $this->assertEquals('active', $vehicle->fresh()->status);
    }

    public function test_speeding_creates_risk_event()
    {
        $driver = \App\Models\Driver::create(['name' => 'John Doe']);
        $vehicle = Vehicle::create([
            'name' => 'Truck 1',
            'license_plate' => 'ABC-123',
            'current_driver_id' => $driver->id,
        ]);

        $this->postJson('/api/telematics', [
            'license_plate' => 'ABC-123',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'speed' => 120, // Over limit
            'heading' => 90,
            'secret' => 'fleetco_secret_2024',
        ]);

        $this->assertDatabaseHas('risk_events', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'type' => 'speeding',
        ]);
    }

    public function test_trip_is_created_on_movement()
    {
        $vehicle = Vehicle::create([
            'name' => 'Truck 1',
            'license_plate' => 'ABC-123',
        ]);

        $this->postJson('/api/telematics', [
            'license_plate' => 'ABC-123',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'speed' => 10,
            'heading' => 90,
            'secret' => 'fleetco_secret_2024',
        ]);

        $this->assertDatabaseHas('trips', [
            'vehicle_id' => $vehicle->id,
            'end_time' => null,
        ]);
    }
}
