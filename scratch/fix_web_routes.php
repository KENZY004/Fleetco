<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

// Use regex to find the root route and the auth middleware group
$pattern = '/Route::get\(\'\/\', function \(\) \{.*?\}\)->name\(\'landing\'\);/s';
$debugRoute = "\n\n// Temporary debug route for Render Free Tier - DELETE AFTER USE\nRoute::get('/debug-db-fix', function () {\n    try {\n        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'FleetSeeder']);\n        \$users = \App\Models\User::all(['name', 'email', 'role']);\n        return response()->json([\n            'status' => 'success',\n            'message' => 'Database seeded and users retrieved',\n            'users' => \$users\n        ]);\n    } catch (\Exception \$e) {\n        return response()->json([\n            'status' => 'error',\n            'message' => \$e->getMessage()\n        ], 500);\n    }\n});";

if (preg_match($pattern, $content, $matches)) {
    $newContent = str_replace($matches[0], $matches[0] . $debugRoute, $content);
    file_put_contents($file, $newContent);
    echo "Success: Debug route injected using regex.\n";
} else {
    echo "Error: Could not match landing route pattern.\n";
}
