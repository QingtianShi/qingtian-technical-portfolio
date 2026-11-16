<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/validation.php';

$valid = [
    'cardName' => 'Cafe 25', 'cardType' => 'Dining',
    'cardValue' => '25.00', 'points' => '2500',
];
$cases = [
    'Valid Gift Card data' => validateCard($valid) === [],
    'Blank Card Name' => isset(validateCard(array_replace($valid, ['cardName' => '  ']))['cardName']),
    'Invalid Card Type' => isset(validateCard(array_replace($valid, ['cardType' => 'Unknown']))['cardType']),
    'Zero Card Value' => isset(validateCard(array_replace($valid, ['cardValue' => '0']))['cardValue']),
    'Negative Card Value' => isset(validateCard(array_replace($valid, ['cardValue' => '-10']))['cardValue']),
    'Invalid Points Required' => isset(validateCard(array_replace($valid, ['points' => '1.5']))['points']),
    'Malformed card ID' => positiveId('1abc') === null && positiveId(['1']) === null,
];
$failed = in_array(false, $cases, true);
if ($failed && PHP_SAPI !== 'cli') {
    http_response_code(500);
}
if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8');
}
foreach ($cases as $label => $ok) {
    echo ($ok ? 'PASS ' : 'FAIL ') . $label . PHP_EOL;
}
exit($failed ? 1 : 0);
