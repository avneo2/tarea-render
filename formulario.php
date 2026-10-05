<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario - UPVM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <div class="bg-white max-w-lg w-full p-8 rounded-2xl shadow-xl text-left border border-gray-200">
        <img src="foto_alex.jpg" alt="Foto de Alex" class="w-24 h-24 mx-auto rounded-full object-cover border-4 border-blue-600 shadow-sm mb-4">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-6 text-center">Encuesta de Satisfacción UPVM</h2>
        
        <form action="procesar.php" method="POST" class="space-y-5">
            
            <!-- Datos del Alumno -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4 mb-6">
                <h3 class="font-bold text-gray-800 border-b pb-2">Datos del Estudiante</h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Matrícula</label>
                    <input type="text" name="matricula" required placeholder="Ej. 123456789" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Carrera</label>
                    <select name="carrera" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">Selecciona tu programa...</option>
                        <option value="Enfermeria">Licenciatura en Enfermería</option>
                        <option value="Terapia Fisica">Licenciatura en Terapia Física</option>
                        <option value="Administracion">Licenciatura en Administración</option>
                        <option value="Alimentos">Ingeniería en Alimentos</option>
                        <option value="Biotecnologia">Ingeniería en Biotecnología</option>
                        <option value="Logistica">Ingeniería en Logística</option>
                        <option value="Mecanica Automotriz">Ingeniería Mecánica Automotriz</option>
                        <option value="Mecatronica">Ingeniería Mecatrónica</option>
                        <option value="Nanotecnologia">Ingeniería en Nanotecnología</option>
                        <option value="IT e Innovacion Digital">Ingeniería en Tecnologías de la Información e Innovación Digital</option>
                        <option value="Industrial">Ingeniería Industrial</option>
                    </select>
                </div>
            </div>

            <!-- Preguntas Cerradas -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">1. ¿Cómo calificas el estado actual de las instalaciones (baños, áreas verdes)?</label>
                <select name="q1_instalaciones" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="Excelente">Excelente</option>
                    <option value="Bueno">Bueno</option>
                    <option value="Regular">Regular</option>
                    <option value="Malo">Malo</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">2. ¿Con qué frecuencia utilizas la biblioteca de la universidad?</label>
                <select name="q2_biblioteca" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="Diario">Diario</option>
                    <option value="A veces">A veces</option>
                    <option value="Nunca">Nunca</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">3. ¿Cómo evalúas el desempeño general de tus profesores este cuatrimestre?</label>
                <select name="q3_profesores" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="Excelente">Excelente</option>
                    <option value="Bueno">Bueno</option>
                    <option value="Regular">Regular</option>
                    <option value="Deficiente">Deficiente</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">4. ¿Consideras que el equipamiento de los laboratorios es adecuado?</label>
                <select name="q4_laboratorios" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="Totalmente de acuerdo">Totalmente de acuerdo</option>
                    <option value="Parcialmente de acuerdo">Parcialmente de acuerdo</option>
                    <option value="En desacuerdo">En desacuerdo</option>
                </select>
            </div>

            <!-- Única Pregunta Abierta -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">5. ¿Qué aspecto administrativo o académico mejorarías en la UPVM?</label>
                <textarea name="q5_mejora" rows="3" required placeholder="Escribe tu sugerencia aquí..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Checkbox de Privacidad Obligatorio -->
            <div class="flex items-start mt-6 mb-4">
                <div class="flex items-center h-5">
                    <input id="privacidad" name="privacidad" type="checkbox" required class="w-5 h-5 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                </div>
                <label for="privacidad" class="ml-3 text-sm font-medium text-gray-700">
                    He leído y acepto el <a href="privacidad.html" target="_blank" class="text-blue-600 hover:underline">Aviso de Privacidad</a> y el tratamiento de mis datos.
                </label>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mt-4">
                Enviar Respuestas
            </button>
        </form>
    </div>
</body>
</html>