<?php
require_once __DIR__ . '/includes/cards.php';

$cards = database()->query('SELECT * FROM GIFTCARD ORDER BY cardId DESC')->fetchAll();
startPage('Gift Card List');
?>
<main>
    <div class="page-heading"><h1>Gift Card List</h1><a class="button" href="/card-add.php">Add Gift Card</a></div>
    <?php if (!$cards): ?><p>No gift cards are available.</p><?php endif; ?>
    <div class="grid">
        <?php foreach ($cards as $card): ?>
            <article class="card">
                <img src="/images/gift-card.svg" alt="" width="360" height="190">
                <div class="card-content">
                    <p class="type"><?= e($card['cardType']) ?></p>
                    <h2><a href="/card-details.php?card_id=<?= e($card['cardId']) ?>"><?= e($card['cardName']) ?></a></h2>
                    <p><?= e(number_format($card['points'])) ?> points required</p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
