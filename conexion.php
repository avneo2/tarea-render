<?php
// Credenciales de tu base de datos en Railway
$host = getenv('MYSQLHOST') ?: 'zephyr.proxy.rlwy.net'; // ej. monorail.proxy.rlwy.net
$port = getenv('MYSQLPORT') ?: '15113';          // ej. 12345
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'FvnMwGbkpBUpXJaGZDPebfGqTlSRIOiM';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$pdo = new PDO(
    $dsn,
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}