<?php
// api/lib.php
session_start();

/* ---------- CONSTANTS ---------- */
const GRID_SIZE = 10;
const SHIPS = [5, 4, 3, 2, 2];

/* ---------- HELPERS ---------- */
function newGame(string $username, string $difficulty): void {
    $_SESSION['game'] = [
        'phase' => 'placement',
        'username' => $username,
        'difficulty' => $difficulty,

        'player' => [
            'ships' => [],
            'hits' => [],
            'misses' => []
        ],

        'enemy' => [
            'ships' => generateFleet(),
            'hits' => [],
            'misses' => []
        ],

        'stats' => [
            'turns' => 0,
            'playerShots' => 0,
            'playerHits' => 0
        ]
    ];
}

/* ---------- FLEET GENERATION ---------- */
function generateFleet(): array {
    $ships = [];
    $occupied = [];

    foreach (SHIPS as $len) {
        while (true) {
            $start = random_int(0, 99);
            $horizontal = random_int(0, 1) === 1;

            $row = intdiv($start, GRID_SIZE);
            $col = $start % GRID_SIZE;

            if ($horizontal && $col + $len > GRID_SIZE) continue;
            if (!$horizontal && $row + $len > GRID_SIZE) continue;

            $ship = [];
            $valid = true;

            for ($i = 0; $i < $len; $i++) {
                $idx = $horizontal
                    ? $start + $i
                    : $start + ($i * GRID_SIZE);

                if (in_array($idx, $occupied, true)) {
                    $valid = false;
                    break;
                }
                $ship[] = $idx;
            }

            if (!$valid) continue;

            foreach ($ship as $i) $occupied[] = $i;
            $ships[] = $ship;
            break;
        }
    }

    return $ships;
}

/* ---------- UTIL ---------- */
function jsonResponse(array $data): void {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function requireGame(): void {
    if (!isset($_SESSION['game'])) {
        jsonResponse(['ok' => false, 'error' => 'No active game']);
    }
}
