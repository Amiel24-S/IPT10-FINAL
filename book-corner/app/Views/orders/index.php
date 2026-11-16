<h1>My orders</h1>

<?php if (!$orders): ?>
    <p class="empty">You haven't ordered anything yet. <a href="<?= url('/books') ?>">Find a book</a></p>
<?php endif; ?>

<?php foreach ($orders as $order): ?>
    <div class="panel order">
        <div class="order-head">
            <strong>Order #<?= $order['id'] ?></strong>
            <span class="status status-<?= e($order['status']) ?>"><?= e($order['status']) ?></span>
            <span class="muted"><?= e(date('M j, Y g:i A', strtotime($order['created_at']))) ?></span>
        </div>
        <?php foreach ($order['items'] as $item): ?>
            <p class="summary-line"><span><?= $item['quantity'] ?> × <?= e($item['title']) ?></span><span><?= peso($item['price'] * $item['quantity']) ?></span></p>
        <?php endforeach; ?>
        <p class="summary-line total"><span>Total</span><span><?= peso($order['total']) ?></span></p>
    </div>
<?php endforeach; ?>
