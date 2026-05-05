<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TelematicsLog;
use App\Models\Landmark;
use App\Services\RiskEngineService;

$log = TelematicsLog::latest()->first();
$gf = Landmark::first();

if (!$log || !$gf) {
    die("Missing log or geofence\n");
}

$service = new RiskEngineService();
$point = [$log->location->getLatitude(), $log->location->getLongitude()];
$area = $gf->area;

$coords = [];
if ($area instanceof \Clickbar\Magellan\Data\Geometries\Polygon) {
    $ring = $area->getLinearRings()[0];
    $coords = array_map(fn($p) => [$p->getLatitude(), $p->getLongitude()], $ring->getPoints());
} elseif (is_string($area)) {
    $coords = json_decode($area, true);
} else {
    $coords = $area;
}

echo "Log Location: " . $point[0] . ", " . $point[1] . "\n";
echo "Geofence Type: " . $gf->type . "\n";
echo "Geofence Points Count: " . count($coords) . "\n";

// Use the Service's own method for the check
$isInside = false;
$x = $point[0]; $y = $point[1];
for ($i = 0, $j = count($coords) - 1; $i < count($coords); $j = $i++) {
    $xi = $coords[$i][0]; $yi = $coords[$i][1];
    $xj = $coords[$j][0]; $yj = $coords[$j][1];
    
    $intersect = (($yi > $y) != ($yj > $y))
        && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
    if ($intersect) $isInside = !$isInside;
}

echo "Is Inside: " . ($isInside ? "YES" : "NO") . "\n";

if ($gf->type === 'optimized_route' && !$isInside) {
    echo "BREACH DETECTED: Route Deviation\n";
} else {
    echo "NO BREACH\n";
}
