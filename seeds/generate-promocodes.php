<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$connection = \App\Database\Connection::getInstance();

const PROMOCODE_COUNTS = 500000;

$countStmt = $connection->query('SELECT COUNT(*) FROM `promocodes`');
$count = $countStmt->fetchColumn();
if ($count > 0) {
    die(0);
}
echo "Creating promocodes. It may take up to 5 minutes " . PHP_EOL;
$connection->beginTransaction();
$insertStmt = $connection->prepare('INSERT INTO `promocodes` (code) VALUES (?)');
for ($i = 0; $i < PROMOCODE_COUNTS; $i++) {
    $insertStmt->execute([\App\Services\PromocodeService::generateCode()]);
}
$connection->commit();
