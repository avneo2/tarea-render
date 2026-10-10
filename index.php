<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio - Aplicaciones Web Orientadas</title>
    <!-- Aquí se inyecta Tailwind CSS directamente desde internet -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    
    <div class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl text-center border border-gray-200">
        <!-- Requisito 1: Tu foto (Con bordes redondeados, sombra y efecto al pasar el mouse) -->
        <img src="foto_alex.jpg" alt="Foto de Alex" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-blue-600 shadow-md mb-6 transition-transform hover:scale-105">
        
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Proyectos - 5to Cuatrimestre</h1>
        <p class="text-gray-500 mb-8 text-sm font-medium">Selecciona la práctica que deseas revisar:</p>
        
        <!-- Contenedor de botones con espaciado uniforme -->
        <div class="flex flex-col gap-4">
            
            <!-- Botón de la encuesta actual -->
            <a href="formulario.php" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📝 1. Encuesta UPVM (Actual)
            </a>
            
            <!-- Botón de la práctica de red -->
            <a href="dos_lados.php" class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                📄 2. Práctica: Dos Lados
            </a>
            
            <!-- Botón a tu proyecto en Render -->
            <a href="https://alex-mapas-upvm.onrender.com/" target="_blank" class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                🗺️ 3. Mapa Interactivo (API Google)
            </a>

            <!-- NUEVO BOTÓN PARA EL VIDEO -->
            <a href="video.php" class="flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                ▶️ 4. Mi Video (Incrustado)
            </a>

        </div>
    </div>

</body>
</html>