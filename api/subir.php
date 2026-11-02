<?php
// Ubicación: Tarea_Render/api/subir.php
require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Usa POST.']);
}

// Candado LOGIN activado: solo usuarios autenticados pueden subir fotos
if (!isset($_SESSION['usuario_id'])) {
    responder(401, ['error' => 'Inicia sesión para subir fotos.']);
}

$foto = $_FILES['foto'] ?? null;
if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
    responder(400, ['error' => 'No llegó ninguna foto válida.']);
}

// Límite de 5 MB (Con signos de multiplicación corregidos)
if ($foto['size'] > 5 * 1024 * 1024) {
    responder(400, ['error' => 'La foto pesa más de 5 MB.']);
}

// Inspección real de bytes MIME con finfo
$tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$tipo  = (new finfo(FILEINFO_MIME_TYPE))->file($foto['tmp_name']);
if (!isset($tipos[$tipo])) {
    responder(400, ['error' => 'El archivo enviado no es una imagen válida.']);
}

$nombreSesion = $_SESSION['nombre'] ?? 'usuario';
$usuario = preg_replace('/[^A-Za-z0-9]/', '', $nombreSesion) ?: 'usuario';
$archivo = $usuario . '_' . date('Ymd-His') . '_' . bin2hex(random_bytes(2)) . '.' . $tipos[$tipo];

// Asegurar que la carpeta subidas exista y tenga permisos en el contenedor de Render
$directorioSubidas = __DIR__ . '/../subidas';
if (!is_dir($directorioSubidas)) {
    mkdir($directorioSubidas, 0777, true);
}

$ruta = $directorioSubidas . '/' . $archivo;
if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
    responder(500, ['error' => 'No se pudo guardar la foto en el servidor.']);
}

$sql = 'INSERT INTO fotos (usuario_id, nombre, archivo, tipo, bytes) VALUES (?, ?, ?, ?, ?)';
try {
    $pdo->prepare($sql)->execute([$_SESSION['usuario_id'], $nombreSesion, $archivo, $tipo, $foto['size']]);
} catch (PDOException $error) {
    @unlink($ruta);
    responder(500, ['error' => 'No se pudo registrar la foto en la base de datos.']);
}

responder(201, ['archivo' => $archivo, 'url' => 'subidas/' . $archivo]);
