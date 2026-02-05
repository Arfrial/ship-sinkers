<?php
require_once 'lib.php';
requireGame();

$game =& $_SESSION['game'];

if ($game['phase'] !== 'placement') {
    jsonResponse(['ok' => false, 'error' => 'Not in placement phase']);
}

$data = json_decode(file_get_contents('php://input'), true);
$ship = $data['ship'] ?? [];

if (count($ship) === 0) {
    jsonResponse(['ok' => false, 'error' => 'Empty ship']);
}

// Bounds & overlap
foreach ($ship as $cell) {
    if ($cell < 0 || $cell > 99) {
        jsonResponse(['ok' => false, 'error' => 'Out of bounds']);
    }

    foreach ($game['player']['ships'] as $existing) {
        if (in_array($cell, $existing, true)) {
            jsonResponse(['ok' => false, 'error' => 'Overlap']);
        }
    }
}

$game['player']['ships'][] = $ship;

// All ships placed?
if (count($game['player']['ships']) === count(SHIPS)) {
    $game['phase'] = 'battle';
}

jsonResponse(['ok' => true, 'phase' => $game['phase']]);
