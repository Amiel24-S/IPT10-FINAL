<h1>Checkout</h1>

<div class="two-col">
    <form method="post" action="<?= url('/checkout') ?>" class="panel form">
        <?= csrf_field() ?>
        <label>Delivery address
            <textarea name="address" rows="4" required placeholder="House no., street, barangay, city"></textarea>
        </label>
        <p class="muted">Payment: cash on delivery</p>
        <button class="btn">Place order · <?= peso($total) ?></button>
    </form>

    <div class="panel">
        <h3>Order summary</h3>
        <?php foreach ($lines as $line): ?>
            <p class="summary-line"><span><?= $line['qty'] ?> × <?= e($line['book']['title']) ?></span><span><?= peso($line['subtotal']) ?></span></p>
        <?php endforeach; ?>
        <p class="summary-line total"><span>Total</span><span><?= peso($total) ?></span></p>
    </div>
</div>
