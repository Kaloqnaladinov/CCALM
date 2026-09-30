<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/functions.php';

$stmt = $pdo->query("SELECT * FROM menu_items WHERE available = 1 ORDER BY category, sort_order, id");
$items = $stmt->fetchAll();
$menu = [];
foreach ($items as $item) $menu[$item['category']][] = $item;

$flash = getFlash();
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>C’est Comme À La Maison — CCALM</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-950 text-stone-100">
<header class="min-h-[70vh] flex items-center px-6 py-16 bg-[radial-gradient(circle_at_top,_#44403c,_#0c0a09_60%)]">
<div class="max-w-5xl mx-auto w-full">
<p class="uppercase tracking-[.3em] text-amber-300 text-sm">Le Marais · Paris 4e</p>
<h1 class="text-5xl md:text-7xl font-serif mt-5 leading-tight">Comme à la maison.<br><span class="text-amber-200">Mais à Paris.</span></h1>
<p class="mt-6 max-w-xl text-stone-300 text-lg">Cuisine familiale, produits de saison, accueil humain — une petite table où chaque service ressemble à un repas chez quelqu’un qu’on aime.</p>
<div class="mt-8 flex gap-3 flex-wrap">
<a href="#reservation" class="bg-amber-200 text-stone-950 px-6 py-3 rounded-full font-semibold">Réserver une table</a>
<a href="#menu" class="border border-stone-600 px-6 py-3 rounded-full">Voir le menu</a>
</div>
</div>
</header>

<main class="max-w-5xl mx-auto px-6 py-20">
<section class="mb-20">
<p class="text-amber-300 uppercase tracking-widest text-sm">L’esprit CCALM</p>
<h2 class="text-4xl font-serif mt-3">Une petite table. Une vraie cuisine. Une vraie rencontre.</h2>
<p class="text-stone-400 mt-5 max-w-2xl">Le menu évolue au fil de la semaine et des produits disponibles. Les plats peuvent être mis à jour directement depuis l’espace administrateur.</p>
</section>

<section id="menu" class="mb-20">
<h2 class="text-4xl font-serif mb-8">Le menu du moment</h2>
<div class="grid md:grid-cols-2 gap-10">
<?php foreach ($menu as $category => $categoryItems): ?>
<div>
<h3 class="text-amber-200 text-xl mb-4"><?= e($category) ?></h3>
<?php foreach ($categoryItems as $item): ?>
<div class="border-b border-stone-800 py-4">
<div class="flex justify-between gap-4"><strong><?= e($item['name']) ?></strong><span><?= number_format((float)$item['price'], 0, ',', ' ') ?> €</span></div>
<p class="text-stone-500 mt-1"><?= e($item['description']) ?></p>
</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>
</section>

<section id="reservation" class="bg-stone-900 rounded-3xl p-7 md:p-10">
<h2 class="text-4xl font-serif">Réserver</h2>
<p class="text-stone-400 mt-2">Choisissez une date puis un créneau disponible.</p>
<?php if ($flash): ?><div class="mt-4 bg-red-950/60 text-red-200 p-4 rounded-xl"><?= e($flash) ?></div><?php endif; ?>

<form method="post" action="reserve.php" class="grid md:grid-cols-2 gap-4 mt-8">
<input name="customer_name" required placeholder="Nom" class="bg-stone-800 rounded-xl px-4 py-3">
<input name="phone" required placeholder="Téléphone" class="bg-stone-800 rounded-xl px-4 py-3">
<input name="email" type="email" placeholder="Email" class="bg-stone-800 rounded-xl px-4 py-3">
<input id="date" name="reservation_date" type="date" required class="bg-stone-800 rounded-xl px-4 py-3">
<select id="time" name="reservation_time" required class="bg-stone-800 rounded-xl px-4 py-3"><option value="">Choisir l’heure</option></select>
<select name="guests" class="bg-stone-800 rounded-xl px-4 py-3">
<?php for ($n=1;$n<=16;$n++): ?><option value="<?= $n ?>"><?= $n ?> personne<?= $n>1?'s':'' ?></option><?php endfor; ?>
</select>
<textarea name="notes" placeholder="Allergies, occasion, demande particulière..." class="bg-stone-800 rounded-xl px-4 py-3 md:col-span-2"></textarea>
<button class="bg-amber-200 text-stone-950 rounded-xl px-5 py-3 font-semibold md:col-span-2">Envoyer la demande</button>
</form>
</section>
</main>

<script>
const date = document.querySelector('#date');
const time = document.querySelector('#time');
date.min = new Date().toISOString().slice(0,10);
date.addEventListener('change', async () => {
    time.innerHTML = '<option value="">Choisir l’heure</option>';
    if (!date.value) return;
    const response = await fetch('api/availability.php?date=' + encodeURIComponent(date.value));
    const slots = await response.json();
    slots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot.time;
        option.textContent = slot.time + ' — ' + slot.remaining + ' places';
        time.appendChild(option);
    });
});
</script>
</body>
</html>
