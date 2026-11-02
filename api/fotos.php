<?php
// Ubicación: Tarea_Render/api/fotos.php
require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión para ver la galería.']);
    exit;
}

$fotos = $pdo->query('SELECT nombre, archivo, creado_en FROM fotos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(array_map(fn($foto) => [
    'url'     => 'subidas/' . $foto['archivo'],
    'usuario' => $foto['nombre'],
    'fecha'   => $foto['creado_en'],
], $fotos));