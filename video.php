<?php
require 'conexion.php';

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
    <title>Mi Video - Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    
    <div class="bg-white max-w-3xl w-full p-8 rounded-2xl shadow-xl text-center border border-gray-200">
        
        <h1 class="text-2xl font-extrabold text-gray-900 mb-6">🎥 Mi Presentación en Video</h1>
        
        <div class="relative w-full overflow-hidden rounded-xl shadow-lg border border-gray-300 mb-8" style="padding-top: 56.25%;">
            <iframe 
                class="absolute top-0 left-0 w-full h-full"
                src="https://www.youtube.com/embed/1iV6QE1jLGM" 
                title="Reproductor de YouTube" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                allowfullscreen>
            </iframe>
        </div>
        
        <div class="flex justify-center gap-4">
            <a href="index.php" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg">
                ⬅️ Regresar al portafolio
            </a>
            <a href="logout.php" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg">
                🚪 Cerrar sesión
            </a>
        </div>
        
    </div>

</body>
</html>