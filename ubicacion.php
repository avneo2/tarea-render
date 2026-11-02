<?php
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$stmtCheck = $pdo->prepare('SELECT rostro_descriptor, fecha_nacimiento FROM usuarios WHERE id = ?');
$stmtCheck->execute([$_SESSION['usuario_id']]);
$datosSeguridad = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (empty($datosSeguridad['rostro_descriptor']) || empty($datosSeguridad['fecha_nacimiento'])) {
    header('Location: bienvenida.php?obligatorio=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Ubicación - OpenStreetMap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #mapa { height: 380px; width: 100%; border-radius: 1rem; z-index: 10; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-3xl w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <?php include 'menu.php'; ?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-2">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">📍 Mi ubicación geográfica</h1>
                <p class="text-xs text-gray-500 mt-1">Geolocalización satelital con OpenStreetMap y Leaflet.</p>
            </div>
            <span class="text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg">
                Sesión: <?= e($_SESSION['nombre']) ?>
            </span>
        </div>

        <button type="button" id="btn-capturar" data-track="Capturar mi Ubicación GPS"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mb-4 flex items-center justify-center gap-2">
            🛰️ Capturar mi ubicación actual
        </button>

        <p id="mensaje" hidden></p>
        <p id="datos" hidden class="bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-xs text-slate-700 font-medium mb-4 text-center"></p>

        <div id="mapa" class="shadow-inner border border-gray-300 mb-6"></div>

        <h2 class="text-base font-bold text-gray-800 mb-3">🕒 Historial de ubicaciones (Últimas 50)</h2>
        <ul id="historial" class="space-y-2 max-h-56 overflow-y-auto pr-1"></ul>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="ubicacion.js"></script>
    <script src="tracker.js"></script>
</body>
</html>