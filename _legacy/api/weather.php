<?php
require_once __DIR__ . '/../config/config.php'; //tu mam autoloadera dla klas
header('Content-type: application/json');

echo json_encode([
    'status' => 'ok'
]);