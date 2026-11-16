<h1>Your cart</h1>

<?php if (!$lines): ?>
    <p class="empty">Your cart is empty. <a href="<?= url('/books') ?>">Browse books</a></p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>Book</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($lines as $line): $b = $line['book']; ?>
            <tr>
                <td><a href="<?= url('/books/' . $b['id']) ?>"><?= e($b['title']) ?></a><br><span class="muted"><?= e($b['author']) ?></span></td>
                <td><?= peso($b['price']) ?></td>
                <td>
                    <form method="post" action="<?= url('/cart/update') ?>" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                        <input type="number" name="qty" value="<?= $line['qty'] ?>" min="0" class="qty">
                        <button class="link-btn">Update</button>
                    </form>
                </td>
                <td><?= peso($line['subtotal']) ?></td>
                <td>
                    <form method="post" action="<?= url('/cart/remove') ?>" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                        <button class="link-btn danger">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><td colspan="3">Total</td><td colspan="2"><strong><?= peso($total) ?></strong></td></tr></tfoot>
    </table>
    <div class="actions">
        <a href="<?= url('/books') ?>" class="btn btn-ghost">Keep shopping</a>
        <a href="<?= url('/checkout') ?>" class="btn">Checkout</a>
    </div>
<?php endif; ?>
