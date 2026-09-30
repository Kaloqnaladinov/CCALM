<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function openingWindows(): array {
    // 0 = Monday ... 6 = Sunday.
    // Update these to the restaurant's final confirmed schedule.
    return [
        0 => [['18:30', '22:00']],
        1 => [['12:00', '14:00'], ['18:30', '22:00']],
        2 => [['12:00', '14:00'], ['18:30', '22:00']],
        3 => [['12:00', '14:00'], ['18:30', '22:00']],
        4 => [['12:00', '14:00'], ['18:30', '22:00']],
        5 => [],
        6 => [],
    ];
}

function isValidSlot(string $date, string $time): bool {
    $dt = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
    if (!$dt || $dt->format('Y-m-d H:i') !== "$date $time") return false;

    foreach (openingWindows()[$dt->format('N') - 1] ?? [] as [$start, $end]) {
        if ($time >= $start && $time <= $end) return true;
    }
    return false;
}

function slotAvailability(PDO $pdo, string $date): array {
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt || $dt->format('Y-m-d') !== $date) return [];

    $weekday = (int)$dt->format('N') - 1;
    $stmt = $pdo->prepare(
        "SELECT reservation_time, COALESCE(SUM(guests),0) AS booked
         FROM reservations
         WHERE reservation_date = ? AND status <> 'cancelled'
         GROUP BY reservation_time"
    );
    $stmt->execute([$date]);

    $booked = [];
    foreach ($stmt as $row) {
        $booked[$row['reservation_time']] = (int)$row['booked'];
    }

    $capacity = (int)($config['seat_capacity'] ?? 16);
    $slots = [];

    foreach (openingWindows()[$weekday] ?? [] as [$start, $end]) {
        [$h, $m] = array_map('intval', explode(':', $start));
        [$eh, $em] = array_map('intval', explode(':', $end));
        $minutes = $h * 60 + $m;
        $finish = $eh * 60 + $em;

        while ($minutes < $finish) {
            $time = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
            $remaining = max(0, $capacity - ($booked[$time] ?? 0));
            if ($remaining > 0) {
                $slots[] = ['time' => $time, 'remaining' => $remaining];
            }
            $minutes += 30;
        }
    }
    return $slots;
}

function flash(string $message): void {
    $_SESSION['flash'] = $message;
}

function getFlash(): ?string {
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}
