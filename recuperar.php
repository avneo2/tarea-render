<?php
require 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['cancelar'])) {
    unset($_SESSION['reset_uid'], $_SESSION['reset_correo'], $_SESSION['reset_token'], $_SESSION['reset_expira']);
    header('Location: recuperar.php');
    exit;
}

if (isset($_SESSION['reset_expira']) && time() > $_SESSION['reset_expira']) {
    unset($_SESSION['reset_uid'], $_SESSION['reset_correo'], $_SESSION['reset_token'], $_SESSION['reset_expira']);
    $error = 'El tiempo de seguridad expiró. Por favor valida tus datos nuevamente.';
} else {
    $error = '';
}

$paso = isset($_SESSION['reset_uid']) ? 2 : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // PASO 1: Verificación exacta e invariable con Fecha de Nacimiento
    if ($accion === 'verificar') {
        $correo           = trim($_POST['correo'] ?? '');
        $sexo             = $_POST['sexo'] ?? '';
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');

        $stmt = $pdo->prepare('SELECT id, correo FROM usuarios WHERE correo = ? AND sexo = ? AND fecha_nacimiento = ?');
        $stmt->execute([$correo, $sexo, $fecha_nacimiento]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['reset_uid']    = $usuario['id'];
            $_SESSION['reset_correo'] = $usuario['correo'];
            $_SESSION['reset_token']  = bin2hex(random_bytes(32));
            $_SESSION['reset_expira'] = time() + 300;
            $paso = 2;
        } else {
            $error = 'Los datos ingresados no coinciden con ninguna cuenta registrada.';
        }
    }

    // PASO 2: Guardar la nueva contraseña encriptada
    elseif ($accion === 'cambiar' && isset($_SESSION['reset_uid'])) {
        $token     = $_POST['token'] ?? '';
        $password  = $_POST['password'] ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        if (!hash_equals($_SESSION['reset_token'] ?? '', $token)) {
            $error = 'Token de seguridad inválido. Intenta de nuevo.';
        } elseif (strlen($password) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif ($password !== $confirmar) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?');
            $stmt->execute([$nuevoHash, $_SESSION['reset_uid']]);

            unset($_SESSION['reset_uid'], $_SESSION['reset_correo'], $_SESSION['reset_token'], $_SESSION['reset_expira']);

            header('Location: login.php?recuperado=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200">
        
        <?php if ($paso === 1): ?>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-sm">
                    🛡️
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Recuperar contraseña</h1>
                <p class="text-gray-500 text-sm mt-1">Confirma los datos exactos de tu cuenta</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                    ⚠️ <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <input type="hidden" name="accion" value="verificar">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico registrado</label>
                    <input type="email" name="correo" required value="<?= e($_POST['correo'] ?? '') ?>"
                        placeholder="ejemplo@correo.com"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sexo</label>
                        <select name="sexo" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                            <option value="">Selecciona…</option>
                            <?php foreach (['Hombre', 'Mujer', 'Otro'] as $opcion): ?>
                                <option <?= ($_POST['sexo'] ?? '') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" required value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                    </div>
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mt-2">
                    Validar identidad
                </button>
            </form>

        <?php else: ?>
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl shadow-sm">
                    🔑
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Nueva contraseña</h1>
                <p class="text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg py-1 px-3 text-xs font-semibold inline-block mt-2">
                    Identidad verificada: <?= e($_SESSION['reset_correo']) ?>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                    ⚠️ <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form id="form-recuperar" method="post" class="space-y-4">
                <input type="hidden" name="accion" value="cambiar">
                <input type="hidden" name="token" value="<?= e($_SESSION['reset_token']) ?>">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva contraseña (mín. 8 caracteres)</label>
                    <input type="password" name="password" minlength="8" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar nueva contraseña</label>
                    <input type="password" name="confirmar" minlength="8" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mt-2">
                    Guardar nueva contraseña
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="recuperar.php?cancelar=1" class="text-xs text-gray-500 hover:text-red-600 font-medium">
                    ✖ Cancelar y usar otro correo
                </a>
            </div>

            <script>
                const formRecuperar = document.getElementById('form-recuperar');
                if (formRecuperar) {
                    formRecuperar.addEventListener('submit', (e) => {
                        if (formRecuperar.password.value !== formRecuperar.confirmar.value) {
                            e.preventDefault();
                            alert('Las contraseñas nuevas no coinciden.');
                        }
                    });
                }
            </script>
        <?php endif; ?>

        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
            <a href="login.php" class="text-sm text-gray-600 hover:text-gray-900 font-semibold">
                ⬅️ Volver al inicio de sesión
            </a>
        </div>
    </main>
</body>
</html>