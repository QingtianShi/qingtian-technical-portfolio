<?php
require_once __DIR__ . '/includes/cards.php';

$db = database();
$card = selectedCard($db);
$id = $card['cardId'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['confirm'] ?? '') !== 'delete') {
        failPage(400, 'Deletion requires confirmation');
    }
    $statement = $db->prepare('DELETE FROM GIFTCARD WHERE cardId = :id');
    $statement->execute(['id' => $id]);
    if (!$statement->rowCount()) {
        failPage(404, 'Gift card not found');
    }
    redirectTo('/card-list.php');
}
startPage('Delete Gift Card');
?>
<main>
    <h1>Delete Gift Card</h1>
    <p>Permanently delete <strong><?= e($card['cardName']) ?></strong> (Card #<?= e($id) ?>)?</p>
    <form method="post" class="actions">
        <button name="confirm" value="delete">Yes, Delete Gift Card</button>
        <a href="/card-details.php?card_id=<?= e($id) ?>">Keep Gift Card</a>
    </form>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
