<form method="post" class="card-form">
    <?php if ($errors): ?>
        <div role="alert">
            <strong>Check the following fields.</strong>
            <ul><?php foreach ($errors as $field => $error): ?>
                <li><a href="#<?= e($field) ?>"><?= e($error) ?></a></li>
            <?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <label for="cardName">Card Name</label>
    <input id="cardName" name="cardName" maxlength="100" value="<?= e($values['cardName']) ?>" required>
    <label for="cardType">Card Type</label>
    <select id="cardType" name="cardType" required>
        <option value="">Choose a type</option>
        <?php foreach (CARD_TYPES as $type): ?>
            <option value="<?= e($type) ?>" <?= $values['cardType'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="cardValue">Card Value</label>
    <input type="number" id="cardValue" name="cardValue" min="0" step="any" value="<?= e($values['cardValue']) ?>" required>
    <label for="points">Points Required</label>
    <input type="number" id="points" name="points" min="1" step="1" value="<?= e($values['points']) ?>" required>
    <div class="actions"><button><?= e($button) ?></button><a href="<?= e($back) ?>">Cancel</a></div>
</form>
