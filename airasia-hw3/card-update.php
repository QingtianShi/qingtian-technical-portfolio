<?php
require_once __DIR__ . '/includes/cards.php';

$db = database();
$card = selectedCard($db);
$id = $card['cardId'];
$back = '/card-details.php?card_id=' . $id;
$values = array_map('strval', array_intersect_key($card, array_flip(CARD_FIELDS)));
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = cardInput($_POST);
    $errors = validateCard($values);
    if (!$errors) {
        $statement = $db->prepare(
            'UPDATE GIFTCARD SET cardName = :cardName, cardType = :cardType,
             cardValue = :cardValue, points = :points WHERE cardId = :id'
        );
        $statement->execute($values + ['id' => $id]);
        if (!$statement->rowCount()) {
            selectedCard($db);
        }
        redirectTo($back);
    }
    http_response_code(422);
}
startPage('Update Gift Card');
$button = 'Save Changes';
?>
<main>
    <h1>Update Gift Card #<?= e($id) ?></h1>
    <?php require_once __DIR__ . '/includes/card-form.php'; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
