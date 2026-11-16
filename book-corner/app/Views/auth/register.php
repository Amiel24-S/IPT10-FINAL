<div class="auth">
    <h1>Create an account</h1>
    <form method="post" action="<?= url('/register') ?>" class="panel form">
        <?= csrf_field() ?>
        <label>Name <input type="text" name="name" required autofocus></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Password <input type="password" name="password" minlength="6" required></label>
        <button class="btn">Sign up</button>
        <p class="muted">Already have one? <a href="<?= url('/login') ?>">Log in</a></p>
    </form>
</div>
