<div class="auth">
    <h1>Log in</h1>
    <form method="post" action="<?= url('/login') ?>" class="panel form">
        <?= csrf_field() ?>
        <label>Email <input type="email" name="email" required autofocus></label>
        <label>Password <input type="password" name="password" required></label>
        <button class="btn">Log in</button>
        <p class="muted">No account yet? <a href="<?= url('/register') ?>">Sign up</a></p>
    </form>
</div>
