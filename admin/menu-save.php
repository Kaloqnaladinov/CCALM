<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../functions.php';
if (!($_SESSION['admin'] ?? false)) { http_response_code(403); exit('Forbidden'); }
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid CSRF token'); }

$id = (int)($_POST['id'] ?? 0);
$values = [
    trim($_POST['category'] ?? ''),
    trim($_POST['name'] ?? ''),
    trim($_POST['description'] ?? ''),
    (float)($_POST['price'] ?? 0),
    isset($_POST['available']) ? 1 : 0,
    (int)($_POST['sort_order'] ?? 0),
];

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE menu_items SET category=?,name=?,description=?,price=?,available=?,sort_order=? WHERE id=?");
    $stmt->execute([...$values, $id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO menu_items (category,name,description,price,available,sort_order) VALUES (?,?,?,?,?,?)");
    $stmt->execute($values);
}
header('Location: index.php'); exit;
