<?php
require 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Protección contra fuerza bruta (Rate Limiting)
$_SESSION['intentos_login'] = $_SESSION['intentos_login'] ?? 0;
$_SESSION['bloqueo_hasta']  = $_SESSION['bloqueo_hasta'] ?? 0;

if (time() < $_SESSION['bloqueo_hasta']) {
    $segundosRestantes = $_SESSION['bloqueo_hasta'] - time();
    $error = "Demasiados intentos fallidos. Por seguridad, espera {$segundosRestantes} segundos.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modo   = $_POST['modo_login'] ?? 'password';
    $correo = trim($_POST['correo'] ?? '');

    if ($modo === 'facial') {
        // AUTENTICACIÓN BIOMÉTRICA EN EL SERVIDOR
        $descriptorRaw = $_POST['rostro_descriptor'] ?? '';
        $vectorEntrante = validarDescriptorFacial($descriptorRaw);

        if (!$vectorEntrante) {
            $error = 'Datos biométricos inválidos. Vuelve a escanear tu rostro.';
        } else {
            $stmt = $pdo->prepare('SELECT id, nombre, rostro_descriptor FROM usuarios WHERE correo = ?');
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || empty($usuario['rostro_descriptor'])) {
                $_SESSION['intentos_login']++;
                $error = 'Este correo no tiene biometría facial configurada o no existe.';
            } else {
                $vectorGuardado = validarDescriptorFacial($usuario['rostro_descriptor']);
                $distancia = calcularDistanciaEuclidiana($vectorEntrante, $vectorGuardado);

                // Umbral estricto de seguridad biométrica <= 0.45
                if ($distancia <= 0.45) {
                    $_SESSION['intentos_login'] = 0;
                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre']     = $usuario['nombre'];
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION['intentos_login']++;
                    $error = 'Verificación facial rechazada. El rostro no coincide con la cuenta.';
                }
            }
        }
    } else {
        // AUTENTICACIÓN POR CONTRASEÑA
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $_SESSION['intentos_login'] = 0;
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre']     = $usuario['nombre'];
            header('Location: index.php');
            exit;
        }
        $_SESSION['intentos_login']++;
        $error = 'Correo o contraseña incorrectos.';
    }

    if ($_SESSION['intentos_login'] >= 5) {
        $_SESSION['bloqueo_hasta'] = time() + 180; // Bloqueo de 3 minutos tras 5 fallos
        $_SESSION['intentos_login'] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200">
        <div class="text-center mb-6">
            <img src="foto_alex.jpg" alt="Foto de Alex" class="w-20 h-20 mx-auto rounded-full object-cover border-2 border-emerald-600 shadow-sm mb-3">
            <h1 class="text-2xl font-extrabold text-gray-900">🔐 Acceso al Portafolio</h1>
            <p class="text-gray-500 text-sm mt-1">Inicia sesión con contraseña o reconocimiento facial</p>
        </div>

        <?php if (isset($_GET['registrado'])): ?>
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                ✅ Cuenta creada con éxito. Ya puedes iniciar sesión.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['recuperado'])): ?>
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                🔑 Contraseña restablecida con éxito. Inicia sesión con tu nueva clave.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                ⚠️ <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form id="form-login" method="post" class="space-y-4">
            <input type="hidden" name="modo_login" id="modo_login" value="password">
            <input type="hidden" name="rostro_descriptor" id="rostro_descriptor">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="correo" id="input-correo" required value="<?= e($_POST['correo'] ?? '') ?>" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
            </div>

            <div id="bloque-password">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-semibold text-gray-700">Contraseña</label>
                    <a href="recuperar.php" class="text-xs text-emerald-600 hover:underline font-semibold">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <input type="password" name="password" id="input-password" required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
            </div>

            <!-- CÁMARA PARA LOGIN FACIAL -->
            <div id="bloque-facial" class="hidden bg-slate-50 p-4 rounded-xl border border-indigo-200 text-center">
                <div class="relative mx-auto w-56 h-44 bg-black rounded-lg overflow-hidden mb-2 border-2 border-indigo-500">
                    <video id="video-login" autoplay muted playsinline class="w-full h-full object-cover"></video>
                </div>
                <p id="estado-login-facial" class="text-xs text-indigo-700 font-medium mb-2">Cargando cámara...</p>
                <button type="button" id="btn-verificar-rostro" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-all shadow">
                    ✨ Verificar mi Rostro y Entrar
                </button>
            </div>

            <button type="submit" id="btn-submit-normal" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mt-2">
                Entrar con Contraseña
            </button>

            <button type="button" id="btn-modo-facial" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold py-2.5 px-4 rounded-xl transition-all text-sm">
                👤 Iniciar sesión con Reconocimiento Facial
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿No tienes cuenta? <a href="registro.php" class="text-emerald-600 font-bold hover:underline">Regístrate aquí</a>
        </p>
    </main>

    <script>
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        const btnModoFacial = document.getElementById('btn-modo-facial');
        const btnVerificar = document.getElementById('btn-verificar-rostro');
        const btnSubmitNormal = document.getElementById('btn-submit-normal');
        const bloquePassword = document.getElementById('bloque-password');
        const bloqueFacial = document.getElementById('bloque-facial');
        const inputPassword = document.getElementById('input-password');
        const inputCorreo = document.getElementById('input-correo');
        const modoLogin = document.getElementById('modo_login');
        const inputDescriptor = document.getElementById('rostro_descriptor');
        const videoLogin = document.getElementById('video-login');
        const estadoLogin = document.getElementById('estado-login-facial');
        const formLogin = document.getElementById('form-login');
        let modelosListos = false;
        let streamLogin = null;

        btnModoFacial.addEventListener('click', async () => {
            if (!inputCorreo.value.trim()) {
                alert('Primero escribe tu correo electrónico arriba para saber qué cuenta verificar.');
                inputCorreo.focus();
                return;
            }
            bloquePassword.classList.add('hidden');
            btnSubmitNormal.classList.add('hidden');
            btnModoFacial.classList.add('hidden');
            bloqueFacial.classList.remove('hidden');
            inputPassword.required = false;
            modoLogin.value = 'facial';

            try {
                estadoLogin.textContent = '⏳ Cargando IA biométrica...';
                if (!modelosListos) {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                    ]);
                    modelosListos = true;
                }
                streamLogin = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                videoLogin.srcObject = streamLogin;
                estadoLogin.textContent = 'Mira a la cámara y presiona Verificar mi Rostro.';
            } catch (e) {
                estadoLogin.textContent = '❌ Error al abrir cámara: ' + e.message;
            }
        });

        btnVerificar.addEventListener('click', async () => {
            estadoLogin.textContent = '🔍 Escaneando geometría facial...';
            const det = await faceapi
                .detectSingleFace(videoLogin, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!det) {
                estadoLogin.textContent = '⚠️ Rostro no detectado. Acomódate frente a la cámara e intenta otra vez.';
                return;
            }

            inputDescriptor.value = JSON.stringify(Array.from(det.descriptor));
            if (streamLogin) {
                streamLogin.getTracks().forEach(t => t.stop());
            }
            estadoLogin.textContent = '✅ Biometría capturada. Validando en el servidor...';
            formLogin.submit();
        });
    </script>
</body>
</html>