<?php
// api/init.php
require_once __DIR__ . '/lib.php';

$input = json_decode(file_get_contents('php://input'), true);

$username = trim($input['username'] ?? '');
$difficulty = $input['difficulty'] ?? '';

$allowed = ['easy', 'medium', 'hard', 'impossible'];

if ($username === '' || !in_array($difficulty, $allowed, true)) {
    jsonResponse([
        'ok' => false,
        'error' => 'Invalid username or difficulty'
    ]);
}

newGame($username, $difficulty);

jsonResponse([
    'ok' => true,
    'phase' => 'placement'
]);
