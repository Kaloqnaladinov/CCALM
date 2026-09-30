<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../functions.php';

if (!($_SESSION['admin'] ?? false)) {
    header('Location: login.php'); exit;
}

$menu = $pdo->query("SELECT * FROM menu_items ORDER BY category, sort_order, id")->fetchAll();
$reservations = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC, reservation_time DESC, id DESC LIMIT 100")->fetchAll();
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-stone-950 text-white p-5 md:p-10"><div class="max-w-7xl mx-auto">
<div class="flex justify-between items-center"><h1 class="text-4xl font-serif">CCALM Control Center</h1><a href="logout.php" class="text-stone-400">Logout</a></div>

<section class="mt-10"><h2 class="text-2xl font-serif mb-4">Menu</h2>
<form method="post" action="menu-save.php" class="grid md:grid-cols-6 gap-2 bg-stone-900 p-4 rounded-2xl">
<input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
<input name="category" placeholder="Category" required class="bg-stone-800 p-2 rounded">
<input name="name" placeholder="Dish" required class="bg-stone-800 p-2 rounded">
<input name="description" placeholder="Description" class="bg-stone-800 p-2 rounded">
<input name="price" type="number" step=".01" placeholder="Price" required class="bg-stone-800 p-2 rounded">
<input name="sort_order" type="number" placeholder="Order" class="bg-stone-800 p-2 rounded">
<button class="bg-amber-200 text-stone-950 rounded">Add</button>
</form>

<div class="mt-4 space-y-2">
<?php foreach ($menu as $i): ?>
<form method="post" action="menu-save.php" class="grid md:grid-cols-7 gap-2 bg-stone-900 p-3 rounded-xl">
<input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= $i['id'] ?>">
<input name="category" value="<?= e($i['category']) ?>" class="bg-stone-800 p-2 rounded">
<input name="name" value="<?= e($i['name']) ?>" class="bg-stone-800 p-2 rounded">
<input name="description" value="<?= e($i['description']) ?>" class="bg-stone-800 p-2 rounded">
<input name="price" value="<?= e((string)$i['price']) ?>" class="bg-stone-800 p-2 rounded">
<input name="sort_order" value="<?= e((string)$i['sort_order']) ?>" class="bg-stone-800 p-2 rounded">
<label class="bg-stone-800 p-2 rounded text-sm"><input type="checkbox" name="available" <?= $i['available'] ? 'checked' : '' ?>> Available</label>
<button class="bg-stone-700 rounded">Save</button>
</form>
<?php endforeach; ?>
</div>
</section>

<section class="mt-12"><h2 class="text-2xl font-serif mb-4">Reservations</h2>
<div class="overflow-x-auto"><table class="w-full text-sm">
<tr class="text-left text-stone-500"><th class="p-2">Date</th><th>Time</th><th>Guest</th><th>Party</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($reservations as $r): ?>
<tr class="border-t border-stone-800"><td class="p-2"><?= e($r['reservation_date']) ?></td><td><?= e($r['reservation_time']) ?></td><td><?= e($r['customer_name']) ?><br><span class="text-stone-500"><?= e($r['phone']) ?></span></td><td><?= (int)$r['guests'] ?></td><td><?= e($r['status']) ?></td>
<td class="py-2"><form method="post" action="reservation-status.php" class="inline"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="confirmed"><button class="text-amber-200 mr-3">Confirm</button></form>
<form method="post" action="reservation-status.php" class="inline"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="cancelled"><button class="text-red-300">Cancel</button></form></td></tr>
<?php endforeach; ?>
</table></div></section>
</div></body></html>
