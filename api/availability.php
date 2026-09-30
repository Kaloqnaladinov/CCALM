<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../functions.php';

$date = $_GET['date'] ?? '';
echo json_encode(slotAvailability($pdo, $date), JSON_UNESCAPED_UNICODE);
