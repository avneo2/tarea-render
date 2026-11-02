<?php
// Ubicación: Tarea_Render/api/guardar.php
require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Usa POST.']);
}

// Candado: solo usuarios con sesión activa pueden guardar coordenadas
if (!isset($_SESSION['usuario_id'])) {
    responder(401, ['error' => 'Inicia sesión para guardar tu ubicación.']);
}

$datos = json_decode(file_get_contents('php://input'), true);
$lat   = $datos['latitud'] ?? null;
$lng   = $datos['longitud'] ?? null;

// Validación de rango geográfico en el servidor
if (!is_numeric($lat) || !is_numeric($lng)
    || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    responder(400, ['error' => 'Coordenadas geográficas inválidas.']);
}

$precision = max(0, min(100000, (int) round((float) ($datos['precision'] ?? 0))));
$usuarioId = $_SESSION['usuario_id'];
$nombre    = $_SESSION['nombre'] ?? 'Usuario';

$sql = 'INSERT INTO ubicaciones (usuario_id, nombre, latitud, longitud, precision_m)
        VALUES (?, ?, ?, ?, ?)';
$pdo->prepare($sql)->execute([$usuarioId, $nombre, $lat, $lng, $precision]);

responder(201, ['id' => (int) $pdo->lastInsertId()]);