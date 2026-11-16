<?php
use App\Services\Cart;
use Spine\Env;
use Spine\Session;

$user  = Session::get('user');
$flash = Session::pullFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Book Corner') ?> · <?= e(Env::get('APP_NAME', 'Book Corner')) ?></title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-inner">
        <a class="brand" href="<?= url('/') ?>"><span class="brand-mark">❦</span> Book Corner</a>
        <nav>
            <a href="<?= url('/books') ?>">Browse</a>
            <a href="<?= url('/cart') ?>">Cart <span class="pill"><?= Cart::count() ?></span></a>
            <?php if ($user): ?>
                <a href="<?= url('/orders') ?>">My orders</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= url('/admin') ?>" class="admin-link">Admin</a>
                <?php endif; ?>
                <form method="post" action="<?= url('/logout') ?>" class="inline">
                    <?= csrf_field() ?>
                    <button class="link-btn">Log out (<?= e($user['name']) ?>)</button>
                </form>
            <?php else: ?>
                <a href="<?= url('/login') ?>">Log in</a>
                <a href="<?= url('/register') ?>" class="btn btn-small">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="wrap">
    <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="footer">
    <div class="wrap">Book Corner · built on the Spine PHP framework · book data from Open Library</div>
</footer>
</body>
</html>
