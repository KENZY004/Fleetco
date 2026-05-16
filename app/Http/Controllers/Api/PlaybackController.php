<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlaybackController extends Controller
{
    public function getHistory(Vehicle $vehicle)
    {
        // Fetch logs for the last 24 hours
        $logs = TelematicsLog::where('vehicle_id', $vehicle->id)
            ->where('captured_at', '>=', Carbon::now('UTC')->subHours(24))
            ->orderBy('captured_at', 'asc')
            ->get();

        // Point Decimation: If we have too many points, sample them to keep the UI snappy
        // Target ~500 points for a smooth but performant replay
        if ($logs->count() > 1000) {
            $skip = ceil($logs->count() / 500);
            $logs = $logs->filter(fn($l, $i) => $i % $skip == 0)->values();
        }

        $path = $logs->map(function($log) {
            $lat = null;
            $lng = null;

            if ($log->location instanceof \Clickbar\Magellan\Data\Geometries\Point) {
                $lat = $log->location->getLatitude();
                $lng = $log->location->getLongitude();
            } elseif (is_string($log->location) && str_contains($log->location, 'POINT')) {
                preg_match('/POINT\((.+) (.+)\)/', $log->location, $matches);
                if (isset($matches[1]) && isset($matches[2])) {
                    $lng = (float) $matches[1];
                    $lat = (float) $matches[2];
                }
            }

            return [
                'lat' => $lat,
                'lng' => $lng,
                'speed' => $log->speed,
                'heading' => $log->heading,
                'time' => $log->captured_at->format('H:i:s'),
                'raw_time' => $log->captured_at->toIso8601String()
            ];
        })->filter(fn($p) => $p['lat'] !== null && $p['lng'] !== null)->values();

        // Fetch incidents for the same period
        $alerts = \App\Models\RiskEvent::where('vehicle_id', $vehicle->id)
            ->where('occurred_at', '>=', Carbon::now('UTC')->subHours(24))
            ->get()
            ->map(function($alert) {
                $lat = null;
                $lng = null;
                $log = $alert->telematicsLog;
                
                if ($log) {
                    if ($log->location instanceof \Clickbar\Magellan\Data\Geometries\Point) {
                        $lat = $log->location->getLatitude();
                        $lng = $log->location->getLongitude();
                    } elseif (is_string($log->location) && str_contains($log->location, 'POINT')) {
                        preg_match('/POINT\((.+) (.+)\)/', $log->location, $matches);
                        if (isset($matches[1]) && isset($matches[2])) {
                            $lng = (float) $matches[1];
                            $lat = (float) $matches[2];
                        }
                    }
                }

                return [
                    'id' => $alert->id,
                    'type' => $alert->type,
                    'lat' => $lat,
                    'lng' => $lng,
                    'time' => $alert->occurred_at->format('H:i:s'),
                    'impact' => $alert->impact_score,
                    'details' => $alert->details
                ];
            });

        return response()->json([
            'vehicle_name' => $vehicle->name,
            'path' => $path,
            'alerts' => $alerts
        ]);
    }
}
