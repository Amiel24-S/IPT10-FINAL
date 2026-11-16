<a href="<?= url('/books') ?>" class="back">← Back to all books</a>

<article class="detail">
    <div class="cover cover-large">
        <img src="<?= e(cover($book, 'L')) ?>" alt="Cover of <?= e($book['title']) ?>" onerror="this.remove()">
        <span class="cover-fallback"><?= e($book['title']) ?></span>
    </div>
    <div>
        <?php if ($book['category']): ?><p class="eyebrow"><?= e($book['category']) ?></p><?php endif; ?>
        <h1><?= e($book['title']) ?></h1>
        <p class="lead">by <?= e($book['author']) ?></p>
        <p class="price price-large"><?= peso($book['price']) ?></p>

        <?php if ($book['description']): ?><p><?= e($book['description']) ?></p><?php endif; ?>

        <dl class="facts">
            <dt>ISBN</dt><dd><?= e($book['isbn']) ?></dd>
            <?php if ($book['publisher']): ?><dt>Publisher</dt><dd><?= e($book['publisher']) ?></dd><?php endif; ?>
            <?php if ($book['year']): ?><dt>Year</dt><dd><?= e($book['year']) ?></dd><?php endif; ?>
            <?php if ($book['pages']): ?><dt>Pages</dt><dd><?= e($book['pages']) ?></dd><?php endif; ?>
            <dt>In stock</dt><dd><?= (int) $book['stock'] ?></dd>
        </dl>

        <?php if ($book['stock'] > 0): ?>
            <form method="post" action="<?= url('/cart/add') ?>" class="add-form">
                <?= csrf_field() ?>
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <input type="number" name="qty" value="1" min="1" max="<?= (int) $book['stock'] ?>">
                <button class="btn">Add to cart</button>
            </form>
        <?php else: ?>
            <p class="flash flash-error">Out of stock</p>
        <?php endif; ?>
    </div>
</article>
