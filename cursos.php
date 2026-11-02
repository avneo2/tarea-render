<?php
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$cursos = [
    ['Python desde cero',        'Variables, ciclos, funciones y archivos.',          '20 h'],
    ['Bases de datos con MySQL', 'Modelado, SQL, consultas con JOIN y GROUP BY.',     '24 h'],
    ['APIs REST con PHP',        'Servicios web, JSON, códigos HTTP y Postman.',      '18 h'],
    ['JavaScript moderno',       'DOM, eventos, fetch y módulos.',                    '22 h'],
    ['Ciberseguridad básica',    'Hash, inyección SQL, XSS y buenas prácticas.',      '16 h'],
    ['Git y GitHub',             'Commits, ramas, pull requests y trabajo en equipo.', '10 h'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Cursos - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-4xl w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <?php include 'menu.php'; ?>
        
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">📚 Catálogo de cursos</h1>
            <p class="text-gray-500 text-sm mt-1">Explora los cursos. Cada clic queda registrado en tiempo real en las métricas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($cursos as [$titulo, $descripcion, $duracion]): ?>
                <article class="bg-slate-50 border border-slate-200 p-5 rounded-xl flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="font-bold text-gray-900 text-base"><?= e($titulo) ?></h2>
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full"><?= e($duracion) ?></span>
                        </div>
                        <p class="text-gray-600 text-xs leading-relaxed mb-4"><?= e($descripcion) ?></p>
                    </div>
                    <button type="button" class="interes w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2.5 px-4 rounded-xl transition-all shadow-sm" data-track="Me interesa: <?= e($titulo) ?>">
                        Me interesa
                    </button>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        document.querySelectorAll('.interes').forEach((boton) => {
            boton.addEventListener('click', () => {
                boton.textContent = '✓ Te avisaremos';
                boton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                boton.classList.add('bg-emerald-600', 'marcado');
            });
        });
    </script>
    <script src="tracker.js"></script>
</body>
</html>