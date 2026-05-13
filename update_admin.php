<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Ensure legacy admin is removed
User::where('email', 'admin@fleetco.com')->delete();

$user = User::updateOrCreate(
    ['email' => 'fleetcosupport@gmail.com'],
    [
        'name' => 'Fleetco Administrator',
        'password' => Hash::make('Fleetco@MinVa'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);
$user->save();

echo "Admin credentials updated successfully.\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
