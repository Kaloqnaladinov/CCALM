<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#reservation'); exit;
}

$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$date = trim($_POST['reservation_date'] ?? '');
$time = trim($_POST['reservation_time'] ?? '');
$guests = (int)($_POST['guests'] ?? 0);
$notes = trim($_POST['notes'] ?? '');

if ($name === '' || $phone === '' || !filter_var($email ?: 'test@example.com', FILTER_VALIDATE_EMAIL) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time) ||
    $guests < 1 || $guests > 16 || !isValidSlot($date, $time)) {
    flash('Veuillez vérifier les informations et choisir un créneau valide.');
    header('Location: index.php#reservation'); exit;
}

$config = require __DIR__ . '/config.php';
$capacity = (int)$config['seat_capacity'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(guests),0) AS booked
         FROM reservations
         WHERE reservation_date=? AND reservation_time=? AND status <> 'cancelled'
         FOR UPDATE"
    );
    $stmt->execute([$date, $time]);
    $booked = (int)$stmt->fetch()['booked'];

    if ($booked + $guests > $capacity) {
        $pdo->rollBack();
        flash('Ce créneau est complet. Merci de choisir une autre heure.');
        header('Location: index.php#reservation'); exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO reservations
        (customer_name, phone, email, reservation_date, reservation_time, guests, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->execute([$name, $phone, $email, $date, $time, $guests, $notes]);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('Une erreur est survenue. Merci de réessayer.');
    header('Location: index.php#reservation'); exit;
}

header('Location: confirmation.php'); exit;
