<?php
// Backfill Driver profile rows for any users with role=driver who have no drivers table entry
$users = App\Models\User::where('role', 'driver')->get();
foreach ($users as $user) {
    $exists = App\Models\Driver::where('user_id', $user->id)->exists();
    if (!$exists) {
        App\Models\Driver::create([
            'user_id'    => $user->id,
            'fleet_id'   => $user->fleet_id,
            'name'       => $user->name,
            'risk_score' => 100,
        ]);
        echo "Created driver profile for: {$user->email}\n";
    } else {
        echo "Already exists: {$user->email}\n";
    }
}
echo "Done.\n";
