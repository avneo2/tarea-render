<?php
// Ubicación: Tarea_Render/foto.php
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
    <title>Subir Foto y Galería - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-3xl w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <?php include 'menu.php'; ?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-2">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">📷 Captura y Subida de Fotos</h1>
                <p class="text-xs text-gray-500 mt-1">Usa tu cámara en vivo o selecciona un archivo validado con finfo MIME.</p>
            </div>
            <span class="text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg">
                Sesión: <strong><?= e($_SESSION['nombre']) ?></strong>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Opción 1: Cámara en vivo -->
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 flex flex-col justify-between text-center">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 mb-2">🎥 Opción 1: Cámara en vivo</h2>
                    <p class="text-xs text-slate-500 mb-4">Captura un fotograma directo desde tu webcam o celular usando HTML5 Canvas.</p>
                    <video id="video" autoplay playsinline muted hidden class="w-full h-48 object-cover rounded-lg bg-black mb-3 border-2 border-indigo-500"></video>
                </div>
                <div class="flex gap-2 justify-center">
                    <button type="button" id="btn-camara" data-track="Foto: Activar Cámara"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-3 rounded-xl transition-all shadow-sm">
                        📷 Activar cámara
                    </button>
                    <button type="button" id="btn-tomar" disabled data-track="Foto: Tomar Foto"
                        class="flex-1 bg-gray-300 text-gray-500 cursor-not-allowed text-xs font-bold py-2.5 px-3 rounded-xl transition-all">
                        ✨ Tomar foto
                    </button>
                </div>
            </div>

            <!-- Opción 2: Elegir archivo del equipo -->
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 flex flex-col justify-between text-center">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 mb-2">📁 Opción 2: Subir desde tu equipo</h2>
                    <p class="text-xs text-slate-500 mb-4">Formatos permitidos: JPG, PNG, WEBP o GIF (Máximo 5 MB).</p>
                </div>
                <label class="cursor-pointer bg-white hover:bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-4 block transition-all">
                    <span class="text-xs font-semibold text-blue-600 block mb-1">Haz clic para elegir una imagen</span>
                    <input type="file" id="archivo" accept="image/*" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 w-full">
                </label>
            </div>
        </div>

        <!-- Vista previa y botón de envío -->
        <div class="text-center mb-6">
            <img id="vista-previa" alt="Vista previa de la foto" hidden class="max-h-64 mx-auto rounded-xl border-2 border-emerald-500 shadow-md mb-4 object-contain bg-black">
            
            <button type="button" id="btn-subir" disabled data-track="Foto: Subir al Servidor"
                class="w-full bg-gray-400 cursor-not-allowed text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md">
                ☁️ Subir foto al servidor
            </button>
            <p id="mensaje" hidden></p>
        </div>

        <hr class="border-gray-200 my-6">

        <h2 class="text-lg font-extrabold text-gray-900 mb-4">🖼️ Galería de fotos subidas</h2>
        <div id="galeria" class="galeria grid grid-cols-2 sm:grid-cols-3 gap-4"></div>
    </main>

    <script src="foto.js"></script>
    <script src="tracker.js"></script>
</body>
</html>