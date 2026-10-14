<?php
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT nombre, correo, sexo, edad, carrera FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200 text-center">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
            👋
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-1">¡Hola, <?= e($usuario['nombre']) ?>!</h1>
        <p class="text-emerald-600 font-medium text-sm mb-6">Iniciaste sesión correctamente.</p>

        <ul class="text-left bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-3 mb-6 text-sm">
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Correo:</span> 
                <span class="text-gray-900"><?= e($usuario['correo']) ?></span>
            </li>
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Sexo:</span> 
                <span class="text-gray-900"><?= e($usuario['sexo']) ?></span>
            </li>
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Edad:</span> 
                <span class="text-gray-900"><?= e($usuario['edad']) ?> años</span>
            </li>
            <li class="flex justify-between">
                <span class="font-bold text-gray-600">Carrera:</span> 
                <span class="text-gray-900 text-right"><?= e($usuario['carrera']) ?></span>
            </li>
        </ul>

        <div class="flex flex-col gap-3">
            <a href="logout.php" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                🚪 Cerrar sesión
            </a>
            <a href="index.php" class="text-sm text-gray-500 hover:text-gray-800 font-medium mt-2">
                ⬅️ Ir al portafolio principal
            </a>
        </div>
    </main>
</body>
</html>