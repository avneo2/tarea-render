<?php
require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre           = trim($_POST['nombre'] ?? '');
    $correo           = trim($_POST['correo'] ?? '');
    $sexo             = $_POST['sexo'] ?? '';
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $carrera          = trim($_POST['carrera'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirmar        = $_POST['confirmar'] ?? '';

    // Calcular la edad exacta al día de hoy a partir de la fecha de nacimiento
    $edad = 0;
    $fechaValida = false;
    if ($fecha_nacimiento !== '') {
        $fechaNacObj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
        if ($fechaNacObj && $fechaNacObj->format('Y-m-d') === $fecha_nacimiento) {
            $hoy = new DateTime('today');
            if ($fechaNacObj < $hoy) {
                $edad = $fechaNacObj->diff($hoy)->y;
                $fechaValida = true;
            }
        }
    }

    if ($nombre === '' || $carrera === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || !in_array($sexo, ['Hombre', 'Mujer', 'Otro'], true) || !$fechaValida || $edad < 15 || $edad > 99) {
        $error = 'Revisa que todos los datos sean válidos (debes tener entre 15 y 99 años).';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO usuarios (nombre, correo, sexo, edad, fecha_nacimiento, carrera, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)';
        try {
            $pdo->prepare($sql)->execute([$nombre, $correo, $sexo, $edad, $fecha_nacimiento, $carrera, $hash]);
            header('Location: login.php?registrado=1');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() !== '23000') {
                throw $ex;
            }
            $error = 'Ese correo ya está registrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">📝 Crear cuenta</h1>
            <p class="text-gray-500 text-sm mt-1">Completa tus datos para registrarte</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                ⚠️ <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form id="form-registro" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre completo</label>
                <input type="text" name="nombre" maxlength="100" required value="<?= e($_POST['nombre'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="correo" maxlength="150" required value="<?= e($_POST['correo'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sexo</label>
                    <select name="sexo" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Selecciona…</option>
                        <?php foreach (['Hombre', 'Mujer', 'Otro'] as $opcion): ?>
                            <option <?= ($_POST['sexo'] ?? '') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" required value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Carrera</label>
                <input type="text" name="carrera" maxlength="100" required value="<?= e($_POST['carrera'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Contraseña (mín. 8 caracteres)</label>
                <input type="password" name="password" minlength="8" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar contraseña</label>
                <input type="password" name="confirmar" minlength="8" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mt-2">
                Registrarme
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿Ya tienes cuenta? <a href="login.php" class="text-blue-600 font-bold hover:underline">Inicia sesión</a>
        </p>
    </main>
    <script src="app.js"></script>
</body>
</html>