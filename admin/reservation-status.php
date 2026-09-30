<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../functions.php';
if (!($_SESSION['admin'] ?? false)) { http_response_code(403); exit('Forbidden'); }
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid CSRF token'); }

$status = $_POST['status'] ?? '';
if (!in_array($status, ['pending','confirmed','cancelled'], true)) { http_response_code(400); exit('Invalid status'); }

$stmt = $pdo->prepare("UPDATE reservations SET status=? WHERE id=?");
$stmt->execute([$status, (int)($_POST['id'] ?? 0)]);
header('Location: index.php'); exit;
