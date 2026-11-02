<?php
// Ubicación: Tarea_Render/api/ubicaciones.php
require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión para ver las ubicaciones.']);
    exit;
}

$filas = $pdo->query(
    'SELECT nombre, latitud, longitud, precision_m, creado_en
     FROM ubicaciones ORDER BY id DESC LIMIT 50'
)->fetchAll(PDO::FETCH_ASSOC);

$ubicaciones = array_map(fn($f) => [
    'nombre'      => $f['nombre'],
    'latitud'     => (float) $f['latitud'],
    'longitud'    => (float) $f['longitud'],
    'precision_m' => (int) $f['precision_m'],
    'creado_en'   => $f['creado_en'],
], $filas);

echo json_encode($ubicaciones);