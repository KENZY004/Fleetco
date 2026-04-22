<?php

namespace App\Listeners;

use App\Events\TelematicsReceived;
use App\Services\RiskEngineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessTelematicsRisk implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected RiskEngineService $riskEngine
    ) {}

    /**
     * Handle the event.
     */
    public function handle(TelematicsReceived $event): void
    {
        $this->riskEngine->analyze($event->log);
    }
}
