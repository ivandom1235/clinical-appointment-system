<?php
// backend/db.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Simple config block (you can move these to a .env later)
$DB_HOST = '127.0.0.1';
$DB_PORT = '3306';
$DB_NAME = 'clinic';
$DB_USER = 'clinic_user';   // or 'root'
$DB_PASS = 'supersecurepw'; // or '' if Laragon root with no password

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  exit('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}
