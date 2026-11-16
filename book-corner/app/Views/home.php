<section class="hero">
    <div>
        <p class="eyebrow">Your neighbourhood bookshop, online</p>
        <h1>Find your next<br>favourite book.</h1>
        <p class="lead">Classics, fantasy and everything in between, delivered to your door.</p>
        <form action="<?= url('/books') ?>" class="search">
            <input type="search" name="q" placeholder="Search by title, author or ISBN">
            <button class="btn">Search</button>
        </form>
    </div>
    <div class="hero-shelf" aria-hidden="true">
        <span style="--h:78%;--c:#7a3b2e"></span><span style="--h:92%;--c:#2f4a3a"></span>
        <span style="--h:70%;--c:#c79a3b"></span><span style="--h:86%;--c:#34495e"></span>
        <span style="--h:64%;--c:#8c5a7a"></span><span style="--h:95%;--c:#5b3a29"></span>
        <span style="--h:74%;--c:#3d6b6b"></span>
    </div>
</section>

<h2 class="section-title">Shop by genre</h2>
<div class="chips">
    <?php foreach ($categories as $cat): ?>
        <a class="chip" href="<?= url('/books?category=' . $cat['id']) ?>"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
</div>

<h2 class="section-title">New on the shelf</h2>
<div class="grid">
    <?php foreach ($latest as $book): ?>
        <?php require BASE_PATH . '/app/Views/partials/book-card.php'; ?>
    <?php endforeach; ?>
</div>
