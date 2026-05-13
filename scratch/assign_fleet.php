<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Fleet;
use App\Models\User;

$fleet = Fleet::updateOrCreate(
    ['name' => 'Fleetco HQ'],
    ['name' => 'Fleetco HQ', 'description' => 'Default operational fleet']
);

$updated = User::where('email', 'fleetcosupport@gmail.com')->update(['fleet_id' => $fleet->id]);

echo "Fleet created/found: ID = " . $fleet->id . PHP_EOL;
echo "Admin user updated: " . ($updated ? 'YES' : 'NO') . PHP_EOL;
echo "Done! Admin now belongs to fleet '{$fleet->name}'" . PHP_EOL;
