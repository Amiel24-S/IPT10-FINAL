<h1>Browse books</h1>

<form class="filters" action="<?= url('/books') ?>">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Title, author or ISBN">
    <select name="category">
        <option value="">All genres</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn">Filter</button>
</form>

<?php if (!$books): ?>
    <p class="empty">No books match your search.</p>
<?php else: ?>
    <p class="muted"><?= count($books) ?> book<?= count($books) === 1 ? '' : 's' ?> found</p>
    <div class="grid">
        <?php foreach ($books as $book): ?>
            <?php require BASE_PATH . '/app/Views/partials/book-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
