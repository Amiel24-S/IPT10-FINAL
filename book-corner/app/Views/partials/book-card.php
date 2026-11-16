<a class="card" href="<?= url('/books/' . $book['id']) ?>">
    <div class="cover">
        <img src="<?= e(cover($book)) ?>" alt="" loading="lazy" onerror="this.remove()">
        <span class="cover-fallback"><?= e($book['title']) ?></span>
    </div>
    <div class="card-body">
        <h3><?= e($book['title']) ?></h3>
        <p class="muted"><?= e($book['author']) ?></p>
        <p class="price"><?= peso($book['price']) ?></p>
    </div>
</a>
