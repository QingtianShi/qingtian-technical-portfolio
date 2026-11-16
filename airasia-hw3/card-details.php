<?php
require_once __DIR__ . '/includes/cards.php';

$card = selectedCard(database());
$id = $card['cardId'];
startPage('Gift Card Details');
?>
<main>
    <a href="/card-list.php">Return to Gift Card List</a>
    <h1><?= e($card['cardName']) ?></h1>
    <section class="detail">
        <img src="/images/gift-card.svg" alt="" width="360" height="190">
        <dl>
            <dt>Card ID</dt><dd><?= e($id) ?></dd>
            <dt>Card Type</dt><dd><?= e($card['cardType']) ?></dd>
            <dt>Card Value</dt><dd><?= e($card['cardValue']) ?></dd>
            <dt>Points Required</dt><dd><?= e(number_format($card['points'])) ?></dd>
        </dl>
    </section>
    <div class="actions">
        <a class="button" href="/card-update.php?card_id=<?= e($id) ?>">Update Gift Card</a>
        <a href="/card-delete.php?card_id=<?= e($id) ?>">Delete Gift Card</a>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
