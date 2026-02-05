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

if (in_array($idx, $game['player']['hits'], true) ||
    in_array($idx, $game['player']['misses'], true)) {
    jsonResponse(['ok' => false, 'error' => 'Duplicate enemy shot']);
}

$game['stats']['turns']++;

$result = applyShot($game['player'], $idx);

if ($result['hit']) {
    $game['player']['hits'][] = $idx;
} else {
    $game['player']['misses'][] = $idx;
}

$gameOver = allSunk($game['player']['ships']);
if ($gameOver) {
    $game['phase'] = 'end';
}

jsonResponse([
    'ok' => true,
    'hit' => $result['hit'],
    'sunk' => $result['sunk'],
    'gameOver' => $gameOver,
    'winner' => $gameOver ? 'enemy' : null
]);
