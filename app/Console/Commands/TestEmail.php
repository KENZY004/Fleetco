<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityAlert;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleet:test-email {email}';

    protected $description = 'Send a high-fidelity test security alert email to a specific address';

    public function handle()
    {
        $recipient = $this->argument('email');
        
        $data = [
            'vehicleName' => 'TRUCK-7742 (Heavy Duty)',
            'driverName' => 'Alex Rivera',
            'incidentType' => 'Geofence Breach',
            'deviation' => '12.4',
        ];

        try {
            $this->info("Initiating secure dispatch to: {$recipient}...");
            
            Mail::to($recipient)->send(new SecurityAlert($data));
            
            $this->info('✅ DISPATCH SUCCESSFUL: Check your inbox (or logs if using log driver).');
        } catch (\Exception $e) {
            $this->error('❌ DISPATCH FAILED: ' . $e->getMessage());
            $this->info('Tip: Make sure your .env mail settings are correct!');
        }
    }
}
