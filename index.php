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
    <title>Portafolio - Aplicaciones Web Orientadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    
    <div class="bg-white max-w-xl w-full p-8 rounded-2xl shadow-xl text-center border border-gray-200 my-8">
        
        <?php include 'menu.php'; ?>

        <img src="foto_alex.jpg" alt="Foto de Alex" class="w-28 h-28 mx-auto rounded-full object-cover border-4 border-blue-600 shadow-md mb-4 transition-transform hover:scale-105">
        
        <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Proyectos - 5to Cuatrimestre</h1>
        <p class="text-emerald-700 font-semibold text-xs mb-6">🟢 Sesión activa: <?= e($_SESSION['nombre'] ?? 'Usuario') ?></p>
        
        <div class="flex flex-col gap-3.5">
            <a href="formulario.php" data-track="Práctica 1: Encuesta UPVM" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📝 1. Encuesta UPVM (Actual)
            </a>
            
            <a href="dos_lados.php" data-track="Práctica 2: Dos Lados" class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📄 2. Práctica: Dos Lados
            </a>
            
            <a href="https://alex-mapas-upvm.onrender.com/" target="_blank" data-track="Práctica 3: Mapa API Google" class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                🗺️ 3. Mapa Interactivo (API Google)
            </a>

            <a href="video.php" data-track="Práctica 4: Video Incrustado" class="flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                ▶️ 4. Mi Video (Incrustado)
            </a>

            <a href="cursos.php" data-track="Práctica 5: Catálogo de Cursos" class="flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📚 5. Catálogo de Cursos (Rastreo de Clics)
            </a>

            <a href="dashboard.php" data-track="Práctica 6: Panel de Métricas" class="flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📊 6. Dashboard de Métricas en Vivo (JSON API)
            </a>
        </div>
    </div>

    <script src="tracker.js"></script>
</body>
</html>