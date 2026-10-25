<?php
require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre            = trim($_POST['nombre'] ?? '');
    $correo            = trim($_POST['correo'] ?? '');
    $sexo              = $_POST['sexo'] ?? '';
    $fecha_nacimiento  = trim($_POST['fecha_nacimiento'] ?? '');
    $carrera           = trim($_POST['carrera'] ?? '');
    $password          = $_POST['password'] ?? '';
    $confirmar         = $_POST['confirmar'] ?? '';
    $rostro_raw        = $_POST['rostro_descriptor'] ?? '';

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

    // CANDADO ESTRICTO EN BACKEND: El vector biométrico es obligatorio
    $vectorValido = validarDescriptorFacial($rostro_raw);

    if ($nombre === '' || $carrera === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || !in_array($sexo, ['Hombre', 'Mujer', 'Otro'], true) || !$fechaValida || $edad < 15 || $edad > 99) {
        $error = 'Revisa que todos los datos sean válidos (debes tener entre 15 y 99 años).';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($vectorValido === null) {
        $error = 'Es obligatorio escanear y registrar tu rostro biométrico para crear una cuenta.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $rostroJson = json_encode($vectorValido);

        $sql = 'INSERT INTO usuarios (nombre, correo, sexo, edad, fecha_nacimiento, carrera, password_hash, rostro_descriptor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        try {
            $pdo->prepare($sql)->execute([$nombre, $correo, $sexo, $edad, $fecha_nacimiento, $carrera, $hash, $rostroJson]);
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
    <title>Registro Biométrico Obligatorio - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">📝 Crear cuenta segura</h1>
            <p class="text-gray-500 text-sm mt-1">Todos los campos y el registro facial son obligatorios</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                ⚠️ <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form id="form-registro" method="post" class="space-y-4">
            <input type="hidden" name="rostro_descriptor" id="rostro_descriptor" required>

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

            <!-- MÓDULO DE RECONOCIMIENTO FACIAL OBLIGATORIO -->
            <div id="caja-biometria" class="bg-amber-50 p-4 rounded-xl border border-amber-300 text-center transition-all">
                <p class="text-xs font-bold text-amber-900 mb-2">🔒 Biometría Facial (Requisito Obligatorio *)</p>
                
                <div id="contenedor-camara" class="hidden relative mx-auto w-56 h-44 bg-black rounded-lg overflow-hidden mb-3 border-2 border-indigo-500">
                    <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                </div>

                <p id="estado-facial" class="text-xs text-amber-800 mb-3 font-medium">
                    Debes escanear tu rostro para habilitar el botón de registro.
                </p>

                <div class="flex gap-2 justify-center">
                    <button type="button" id="btn-activar-camara" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-all shadow">
                        📷 Activar Cámara para Escaneo
                    </button>
                    <button type="button" id="btn-capturar-rostro" class="hidden bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-all shadow">
                        ✨ Capturar Mi Rostro
                    </button>
                </div>
            </div>

            <!-- Botón deshabilitado hasta que se capture el rostro -->
            <button type="submit" id="btn-registrar" disabled
                class="w-full bg-gray-400 cursor-not-allowed text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md mt-2">
                🔒 Escanea tu rostro para registrarte
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿Ya tienes cuenta? <a href="login.php" class="text-blue-600 font-bold hover:underline">Inicia sesión</a>
        </p>
    </main>

    <script src="app.js"></script>
    <script>
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        const btnActivar = document.getElementById('btn-activar-camara');
        const btnCapturar = document.getElementById('btn-capturar-rostro');
        const btnRegistrar = document.getElementById('btn-registrar');
        const cajaBiometria = document.getElementById('caja-biometria');
        const contenedorCamara = document.getElementById('contenedor-camara');
        const video = document.getElementById('video');
        const estadoFacial = document.getElementById('estado-facial');
        const inputDescriptor = document.getElementById('rostro_descriptor');
        let modelosCargados = false;
        let streamActual = null;

        btnActivar.addEventListener('click', async () => {
            try {
                btnActivar.disabled = true;
                estadoFacial.textContent = '⏳ Cargando redes neuronales de IA...';
                if (!modelosCargados) {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                    ]);
                    modelosCargados = true;
                }
                streamActual = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = streamActual;
                contenedorCamara.classList.remove('hidden');
                btnActivar.classList.add('hidden');
                btnCapturar.classList.remove('hidden');
                estadoFacial.textContent = 'Mira de frente a la cámara con buena iluminación y presiona Capturar Mi Rostro.';
            } catch (err) {
                btnActivar.disabled = false;
                estadoFacial.textContent = '❌ No se pudo acceder a la cámara: ' + err.message;
            }
        });

        btnCapturar.addEventListener('click', async () => {
            estadoFacial.textContent = '🔍 Analizando 128 puntos biométricos...';
            const deteccion = await faceapi
                .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!deteccion) {
                estadoFacial.textContent = '⚠️ No se detectó tu rostro claramente. Acércate e intenta de nuevo.';
                return;
            }

            inputDescriptor.value = JSON.stringify(Array.from(deteccion.descriptor));
            if (streamActual) {
                streamActual.getTracks().forEach(t => t.stop());
            }
            contenedorCamara.classList.add('hidden');
            btnCapturar.classList.add('hidden');

            // Cambiar estilo a verde verificado y desbloquear botón de envío
            cajaBiometria.classList.remove('bg-amber-50', 'border-amber-300');
            cajaBiometria.classList.add('bg-emerald-50', 'border-emerald-300');
            estadoFacial.innerHTML = '<span class="text-emerald-700 font-bold">✅ ¡Biometría facial verificada y cifrada!</span>';

            btnRegistrar.disabled = false;
            btnRegistrar.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btnRegistrar.classList.add('bg-blue-600', 'hover:bg-blue-700', 'hover:shadow-lg');
            btnRegistrar.textContent = '✅ Completar Registro';
        });
    </script>
</body>
</html>