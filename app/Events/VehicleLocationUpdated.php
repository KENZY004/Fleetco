<?php

namespace App\Events;

use App\Models\TelematicsLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vehicleId;
    public $lat;
    public $lng;
    public $heading;
    public $speed;
    public $status;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(TelematicsLog $log)
    {
        $vehicle = $log->vehicle->refresh();
        $this->vehicleId = $log->vehicle_id;
        $this->lat = $log->location->getLatitude();
        $this->lng = $log->location->getLongitude();
        $this->heading = $log->heading;
        $this->speed = $log->speed;
        $this->status = $vehicle->status ?? 'active';
        $this->timestamp = $log->captured_at;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('fleet-updates'),
        ];
    }
}
