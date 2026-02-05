<?php
require_once 'lib.php';
requireGame();

jsonResponse([
    'ok' => true,
    'game' => $_SESSION['game']
]);
