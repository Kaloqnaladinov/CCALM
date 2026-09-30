<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../functions.php';
$config = require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals((string)$config['admin_password'], (string)($_POST['password'] ?? ''))) {
        $_SESSION['admin'] = true;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: index.php'); exit;
    }
    flash('Mot de passe incorrect.');
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-stone-950 text-white min-h-screen grid place-items-center">
<form method="post" class="bg-stone-900 p-8 rounded-2xl w-full max-w-sm"><h1 class="text-2xl font-serif">CCALM Admin</h1>
<?php if ($m=getFlash()): ?><p class="text-red-300 mt-3"><?= e($m) ?></p><?php endif; ?>
<input name="password" type="password" placeholder="Password" class="w-full mt-6 bg-stone-800 p-3 rounded-xl">
<button class="w-full mt-3 bg-amber-200 text-stone-950 p-3 rounded-xl">Sign in</button></form>
</body></html>
