<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixOrphanAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleetco:fix-orphan-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns missing fleet_ids and marks drivers without fleets as unassigned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing orphan accounts...');

        // Assign fleet_id to managers missing one
        $managers = \App\Models\User::where('role', 'fleet_manager')->whereNull('fleet_id')->get();
        foreach ($managers as $manager) {
            $fleetName = $manager->name ? $manager->name . "'s Fleet" : 'Default Fleet';
            $fleet = \App\Models\Fleet::create(['name' => $fleetName]);
            $manager->update(['fleet_id' => $fleet->id]);
            $this->info("Created fleet '{$fleetName}' for manager ID {$manager->id}");
        }

        // Mark drivers without fleets as role = 'unassigned'
        $drivers = \App\Models\User::where('role', 'driver')->whereNull('fleet_id')->get();
        foreach ($drivers as $driver) {
            $driver->update(['role' => 'unassigned']);
            $this->info("Marked driver ID {$driver->id} as unassigned");
        }

        $this->info('Done.');
    }
}
