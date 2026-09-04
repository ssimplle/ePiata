<?php
$servername = "localhost";
$dbname = "epiata";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$servername};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'A apărut o eroare la accesarea bazei de date.';
    exit;
}
