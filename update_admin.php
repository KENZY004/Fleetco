<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('email', 'admin@fleetco.com')->first();

if (!$user) {
    $user = User::where('email', 'fleetco.support@gmail.com')->first();
}

if (!$user) {
    $user = new User();
}

$user->name = 'Fleetco Administrator';
$user->email = 'fleetco.support@gmail.com';
$user->password = Hash::make('Fleetco@MinVa');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();

echo "Admin credentials updated successfully.\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
