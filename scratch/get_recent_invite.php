<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invites = \App\Models\DriverInvitation::valid()->latest()->get();
if ($invites->count() > 0) {
    foreach ($invites as $invite) {
        echo "Driver: " . $invite->email . PHP_EOL;
        echo "Link:   " . route('register.invite', ['token' => $invite->token]) . PHP_EOL;
        echo "-----------------------------------" . PHP_EOL;
    }
} else {
    echo "No pending invitations found." . PHP_EOL;
}
