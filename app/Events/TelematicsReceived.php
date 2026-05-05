<?php

namespace App\Events;

use App\Models\TelematicsLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelematicsReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TelematicsLog $log
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('fleet-monitoring');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'telematics.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'log' => $this->log->toArray(),
            'current_odometer' => $this->log->vehicle->current_odometer ?? 0,
        ];
    }
}
