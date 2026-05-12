<?php
$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);

// Remove the verified middleware wrapper from the invite route
$content = preg_replace(
    '/\/\/ Fleet Manager invitation.*?Route::middleware\(\[.verified.\]\)->group\(function \(\) \{.*?Route::post.*?fleet\.invite\.send.*?\}\);/s',
    "// Fleet Manager invitation\n        Route::post('/fleet/invite/send', [App\\Http\\Controllers\\InviteController::class, 'send'])->name('fleet.invite.send');",
    $content
);

file_put_contents($file, $content);
echo "Done! Route fixed.\n";
