<div class="page-head">
    <h1>Admin dashboard</h1>
    <div>
        <a href="<?= url('/admin/orders') ?>" class="btn btn-ghost">View orders</a>
        <a href="<?= url('/admin/books/new') ?>" class="btn">+ Add book</a>
    </div>
</div>

<div class="stats">
    <div class="stat"><span><?= (int) $stats['books'] ?></span>Books</div>
    <div class="stat"><span><?= (int) $stats['orders'] ?></span>Orders</div>
    <div class="stat"><span><?= peso($stats['revenue']) ?></span>Revenue</div>
</div>

<table class="table">
    <thead><tr><th></th><th>Title</th><th>Genre</th><th>Price</th><th>Stock</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><img class="thumb" src="<?= e(cover($book, 'S')) ?>" alt="" onerror="this.style.visibility='hidden'"></td>
            <td><?= e($book['title']) ?><br><span class="muted"><?= e($book['author']) ?> · <?= e($book['isbn']) ?></span></td>
            <td><?= e($book['category'] ?? '—') ?></td>
            <td><?= peso($book['price']) ?></td>
            <td class="<?= $book['stock'] < 5 ? 'low' : '' ?>"><?= (int) $book['stock'] ?></td>
            <td class="row-actions">
                <a href="<?= url('/admin/books/' . $book['id'] . '/edit') ?>">Edit</a>
                <form method="post" action="<?= url('/admin/books/' . $book['id'] . '/delete') ?>" class="inline"
                      onsubmit="return confirm('Delete this book?')">
                    <?= csrf_field() ?>
                    <button class="link-btn danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
