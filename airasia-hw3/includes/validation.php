<?php
declare(strict_types=1);

const CARD_TYPES = ['Dining', 'Shopping', 'Travel'];
const CARD_FIELDS = ['cardName', 'cardType', 'cardValue', 'points'];

function positiveId(mixed $value): ?int
{
    if (!is_string($value) || !preg_match('/^[1-9][0-9]*$/D', $value)) {
        return null;
    }
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

function cardInput(array $data): array
{
    $values = [];
    foreach (CARD_FIELDS as $field) {
        $values[$field] = is_string($data[$field] ?? null) ? trim($data[$field]) : '';
    }
    return $values;
}

function validateCard(array $input): array
{
    $card = cardInput($input);
    $errors = [];
    if ($card['cardName'] === '' || preg_match('/^.{1,100}$/usD', $card['cardName']) !== 1) {
        $errors['cardName'] = 'Enter a card name of 1 to 100 characters.';
    }
    if (!in_array($card['cardType'], CARD_TYPES, true)) {
        $errors['cardType'] = 'Choose an available card type.';
    }
    if (!is_numeric($card['cardValue']) || !is_finite((float) $card['cardValue']) || (float) $card['cardValue'] <= 0) {
        $errors['cardValue'] = 'Enter a positive finite amount.';
    }
    if (positiveId($card['points']) === null) {
        $errors['points'] = 'Enter a positive whole number of points.';
    }
    return $errors;
}

function e(string|int|float $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
