import os

path = 'routes/web.php'
# Try different encodings
for enc in ['utf-8', 'windows-1252', 'latin-1']:
    try:
        with open(path, 'r', encoding=enc) as f:
            content = f.read()
        print(f"Successfully read with {enc}")
        break
    except UnicodeDecodeError:
        continue
else:
    print("Failed to decode file with all attempts")
    exit(1)

new_routes = """
    // Fleet Route Management (Admin/Manager)
    Route::prefix('fleet/routes')->name('fleet.routes.')->group(function() {
        Route::get('/', [App\Http\Controllers\Fleet\RouteController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Fleet\RouteController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Fleet\RouteController::class, 'store'])->name('store');
        Route::post('/{id}/assign', [App\Http\Controllers\Fleet\RouteController::class, 'assign'])->name('assign');
    });

    // Driver Route Actions
    Route::post('/driver/route/{routeId}/waypoint/{order}/reach', [App\Http\Controllers\Driver\RouteController::class, 'markWaypointReached'])->name('driver.route.waypoint.reach');
"""

target = "    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');\n});"
replacement = "    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');" + new_routes + "});"

if target in content:
    new_content = content.replace(target, replacement)
    with open(path, 'w', encoding=enc) as f:
        f.write(new_content)
    print("Successfully updated routes/web.php")
else:
    # Try with different whitespace/newlines
    import re
    pattern = r"Route::delete\('/profile', \[ProfileController::class, 'destroy'\]\)->name\('profile\.destroy'\);\s*\}\);"
    if re.search(pattern, content):
        new_content = re.sub(pattern, "Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');" + new_routes + "});", content)
        with open(path, 'w', encoding=enc) as f:
            f.write(new_content)
        print("Successfully updated routes/web.php using regex")
    else:
        print("Target not found in routes/web.php")
