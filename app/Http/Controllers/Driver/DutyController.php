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
        return $this->logStatus('on_duty');
    }

    public function takeBreak()
    {
        return $this->logStatus('break');
    }

    public function goOffDuty()
    {
        return $this->logStatus('off_duty');
    }
}
