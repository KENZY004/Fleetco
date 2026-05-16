<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DutyLog;
use Illuminate\Http\Request;

class DutyController extends Controller
{
    private function checkDriverRole()
    {
        if (!auth()->check() || auth()->user()->role !== 'driver') {
            abort(403, 'Unauthorized action.');
        }
    }

    private function logStatus($status)
    {
        $this->checkDriverRole();
        $user = auth()->user();

        // Close any currently open logs
        DutyLog::where('driver_id', $user->id)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        // Create new log
        DutyLog::create([
            'driver_id' => $user->id,
            'status' => $status,
            'started_at' => now(),
        ]);

        return back()->with('status', 'Status updated.');
    }

    public function goOnDuty()
    {
        $user = auth()->user();
        if ($user->driver) {
            // Find the vehicle assigned to this driver
            $vehicle = \App\Models\Vehicle::where('current_driver_id', $user->driver->id)->first();
            
            if ($vehicle) {
                // Create an instant Trip record so it shows up in history immediately (only if one doesn't exist)
                \App\Models\Trip::firstOrCreate(
                    ['vehicle_id' => $vehicle->id, 'end_time' => null],
                    ['driver_id' => $user->driver->id, 'start_time' => now()]
                );
            }
        }
        return $this->logStatus('on_duty');
    }

    public function takeBreak()
    {
        return $this->logStatus('break');
    }

    public function goOffDuty()
    {
        $user = auth()->user();
        if ($user->driver) {
            $vehicle = \App\Models\Vehicle::where('current_driver_id', $user->driver->id)->first();
            if ($vehicle) {
                // Stop the active trip session but KEEP the driver link so they can still report issues
                app(\App\Services\TelemetryProcessor::class)->stopSession($vehicle);
            }
        }
        return $this->logStatus('off_duty');
    }
}
