<?php
require_once __DIR__ . '/includes/cards.php';

$values = cardInput($_POST);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateCard($values);
    if (!$errors) {
        $statement = database()->prepare(
            'INSERT INTO GIFTCARD (cardName, cardType, cardValue, points)
             VALUES (:cardName, :cardType, :cardValue, :points)'
        );
        $statement->execute($values);
        redirectTo('/card-list.php');
    }
    http_response_code(422);
}
startPage('Add Gift Card');
$button = 'Add Gift Card';
$back = '/card-list.php';
?>
<main>
    <h1>Add Gift Card</h1>
    <?php require_once __DIR__ . '/includes/card-form.php'; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
