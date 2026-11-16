<a href="<?= url('/admin') ?>" class="back">← Back to dashboard</a>
<h1><?= e($title) ?></h1>

<?php if (empty($book['id'])): ?>
    <!-- Step 1: type an ISBN and let Open Library fill in the rest -->
    <form method="get" action="<?= url('/admin/books/new') ?>" class="panel lookup">
        <label>Have an ISBN? Fetch the details from Open Library
            <span class="lookup-row">
                <input type="text" name="isbn" value="<?= e($book['isbn']) ?>" placeholder="e.g. 9780140328721">
                <button class="btn">Fetch details</button>
            </span>
        </label>
        <?php if ($apiMessage): ?>
            <p class="flash flash-<?= e($apiMessage['type']) ?>"><?= e($apiMessage['text']) ?></p>
        <?php endif; ?>
    </form>
<?php endif; ?>

<!-- Step 2: check the details, add a price and save -->
<form method="post" action="<?= url($action) ?>" class="panel form book-form">
    <?= csrf_field() ?>
    <div class="form-cover">
        <?php if (!empty($book['isbn']) || !empty($book['cover_url'])): ?>
            <img src="<?= e(cover($book)) ?>" alt="" onerror="this.remove()">
        <?php endif; ?>
    </div>
    <div class="form-grid">
        <label>ISBN <input name="isbn" value="<?= e($book['isbn']) ?>" required></label>
        <label>Genre
            <select name="category_id">
                <option value="">—</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $book['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="span-2">Title <input name="title" value="<?= e($book['title']) ?>" required></label>
        <label class="span-2">Author <input name="author" value="<?= e($book['author']) ?>" required></label>
        <label>Publisher <input name="publisher" value="<?= e($book['publisher']) ?>"></label>
        <label>Year <input type="number" name="year" value="<?= e($book['year']) ?>"></label>
        <label>Pages <input type="number" name="pages" value="<?= e($book['pages']) ?>"></label>
        <label>Price (₱) <input type="number" step="0.01" name="price" value="<?= e($book['price']) ?>" required></label>
        <label>Stock <input type="number" name="stock" value="<?= e($book['stock']) ?>"></label>
        <label>Cover URL <input name="cover_url" value="<?= e($book['cover_url']) ?>"></label>
        <label class="span-2">Description <textarea name="description" rows="3"><?= e($book['description']) ?></textarea></label>
    </div>
    <button class="btn">Save book</button>
</form>
