<?php
require 'conexion.php';

// CANDADO DE SEGURIDAD: Si no hay sesión activa, rebota al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
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
    
    <div class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl text-center border border-gray-200">
        
        <!-- Barra superior de sesión activa -->
        <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 px-4 py-2.5 rounded-xl mb-6 text-xs">
            <span class="text-emerald-800 font-semibold truncate">
                🟢 Hola, <?= e($_SESSION['nombre'] ?? 'Usuario') ?>
            </span>
            <div class="flex items-center gap-3 shrink-0">
                <a href="bienvenida.php" class="text-blue-600 hover:underline font-bold">Mi Perfil</a>
                <span class="text-gray-300">|</span>
                <a href="logout.php" class="text-red-600 hover:underline font-bold">Salir 🚪</a>
            </div>
        </div>

        <!-- Tu foto -->
        <img src="foto_alex.jpg" alt="Foto de Alex" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-blue-600 shadow-md mb-6 transition-transform hover:scale-105">
        
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Proyectos - 5to Cuatrimestre</h1>
        <p class="text-gray-500 mb-8 text-sm font-medium">Selecciona la práctica que deseas revisar:</p>
        
        <!-- Contenedor de prácticas -->
        <div class="flex flex-col gap-4">
            
            <a href="formulario.php" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📝 1. Encuesta UPVM (Actual)
            </a>
            
            <a href="dos_lados.php" class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📄 2. Práctica: Dos Lados
            </a>
            
            <a href="https://alex-mapas-upvm.onrender.com/" target="_blank" class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                🗺️ 3. Mapa Interactivo (API Google)
            </a>

            <a href="video.php" class="flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                ▶️ 4. Mi Video (Incrustado)
            </a>

        </div>
    </div>

</body>
</html>