<?php

namespace App\Events;

use App\Models\TelematicsLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelematicsReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TelematicsLog $log
    ) {}
}
