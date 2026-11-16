<a href="<?= url('/admin') ?>" class="back">← Back to dashboard</a>
<h1>All orders</h1>

<?php if (!$orders): ?>
    <p class="empty">No orders yet.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>#</th><th>Customer</th><th>Address</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= $o['id'] ?></td>
                <td><?= e($o['customer']) ?></td>
                <td><?= e($o['address']) ?></td>
                <td><?= peso($o['total']) ?></td>
                <td><span class="status status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
                <td><?= e(date('M j, Y', strtotime($o['created_at']))) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
