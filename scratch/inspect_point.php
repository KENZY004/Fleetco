<?php
require __DIR__.'/vendor/autoload.php';
$p = Clickbar\Magellan\Data\Geometries\Point::makeGeodetic(19.0, 72.0);
echo "Class: " . get_class($p) . "\n";
print_r($p);
