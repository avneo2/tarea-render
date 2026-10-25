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
// Validar que el descriptor tenga exactamente 128 puntos biométricos numéricos
function validarDescriptorFacial($jsonStr)
{
    if (!is_string($jsonStr) || strlen($jsonStr) < 200 || strlen($jsonStr) > 5000) {
        return null;
    }
    $arr = json_decode($jsonStr, true);
    if (!is_array($arr) || count($arr) !== 128) {
        return null;
    }
    foreach ($arr as $val) {
        if (!is_numeric($val) || $val < -2.0 || $val > 2.0) {
            return null;
        }
    }
    return array_map('floatval', $arr);
}

// Calcular Distancia Euclidiana en el servidor (Umbral seguro <= 0.45)
function calcularDistanciaEuclidiana(array $vec1, array $vec2): float
{
    $suma = 0.0;
    for ($i = 0; $i < 128; $i++) {
        $diff = $vec1[$i] - $vec2[$i];
        $suma += $diff * $diff;
    }
    return sqrt($suma);
}