<?php
// Ubicación: Tarea_Render/api/eventos.php
require __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$datos = json_decode(file_get_contents('php://input'), true);

$tipo       = $datos['tipo'] ?? '';
$visitante  = $datos['visitante_id'] ?? '';
$sesion     = $datos['sesion_id'] ?? '';
$patronUuid = '/^[0-9a-f-]{36}$/';

if (!is_array($datos) || !in_array($tipo, ['vista', 'clic', 'tiempo'], true)
    || !preg_match($patronUuid, $visitante) || !preg_match($patronUuid, $sesion)) {
    http_response_code(400);
    exit;
}

// Función segura compatible con cualquier contenedor en la nube
function cortarTexto($texto, $largo) {
    return function_exists('mb_substr') ? mb_substr($texto, 0, $largo) : substr($texto, 0, $largo);
}

$pagina   = cortarTexto((string) ($datos['pagina'] ?? ''), 200);
$elemento = $tipo === 'clic' ? cortarTexto((string) ($datos['elemento'] ?? ''), 100) : null;
$segundos = $tipo === 'tiempo' ? max(0, min(3600, (int) ($datos['segundos'] ?? 0))) : null;

$usuarioId = $_SESSION['usuario_id'] ?? null;

$sql = 'INSERT INTO eventos (visitante_id, sesion_id, usuario_id, tipo, pagina, elemento, segundos)
        VALUES (?, ?, ?, ?, ?, ?, ?)';
$pdo->prepare($sql)->execute([$visitante, $sesion, $usuarioId, $tipo, $pagina, $elemento, $segundos]);

http_response_code(204);