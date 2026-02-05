<?php
require_once 'lib.php';
requireGame();

$game =& $_SESSION['game'];

if ($game['phase'] !== 'battle') {
    jsonResponse(['ok' => false, 'error' => 'Not in battle']);
}

$data = json_decode(file_get_contents('php://input'), true);
$idx = $data['index'] ?? -1;

if ($idx < 0 || $idx > 99) {
    jsonResponse(['ok' => false, 'error' => 'Invalid index']);
}

if (in_array($idx, $game['enemy']['hits'], true) ||
    in_array($idx, $game['enemy']['misses'], true)) {
    jsonResponse(['ok' => false, 'error' => 'Duplicate shot']);
}

$game['stats']['playerShots']++;

$result = applyShot($game['enemy'], $idx);

if ($result['hit']) {
    $game['enemy']['hits'][] = $idx;
    $game['stats']['playerHits']++;
} else {
    $game['enemy']['misses'][] = $idx;
}

$gameOver = allSunk($game['enemy']['ships']);
if ($gameOver) {
    $game['phase'] = 'end';
}

jsonResponse([
    'ok' => true,
    'hit' => $result['hit'],
    'sunk' => $result['sunk'],
    'gameOver' => $gameOver,
    'winner' => $gameOver ? 'player' : null
]);
