<?php
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$error   = '';

// Procesar actualización de Fecha de Nacimiento o Rostro desde el perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaFecha = trim($_POST['fecha_nacimiento'] ?? '');
    $rostroRaw  = $_POST['rostro_descriptor'] ?? '';

    if ($nuevaFecha !== '') {
        $nacObj = DateTime::createFromFormat('Y-m-d', $nuevaFecha);
        $hoy = new DateTime('today');
        if ($nacObj && $nacObj < $hoy) {
            $edadCalc = $nacObj->diff($hoy)->y;
            $stmtUpd = $pdo->prepare('UPDATE usuarios SET fecha_nacimiento = ?, edad = ? WHERE id = ?');
            $stmtUpd->execute([$nuevaFecha, $edadCalc, $_SESSION['usuario_id']]);
            $mensaje = '✅ Datos actualizados correctamente.';
        }
    }

    if ($rostroRaw !== '') {
        $vectorValido = validarDescriptorFacial($rostroRaw);
        if ($vectorValido) {
            $stmtFace = $pdo->prepare('UPDATE usuarios SET rostro_descriptor = ? WHERE id = ?');
            $stmtFace->execute([json_encode($vectorValido), $_SESSION['usuario_id']]);
            $mensaje = '✅ ¡Tu biometría facial quedó vinculada a tu cuenta!';
        } else {
            $error = '⚠️ No se pudo guardar el rostro. Intenta de nuevo.';
        }
    }
}

$stmt = $pdo->prepare('SELECT nombre, correo, sexo, edad, fecha_nacimiento, carrera, rostro_descriptor FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$edadExacta = $usuario['edad'];
if (!empty($usuario['fecha_nacimiento'])) {
    $nac = new DateTime($usuario['fecha_nacimiento']);
    $hoy = new DateTime('today');
    $edadExacta = $nac->diff($hoy)->y;
}
$tieneRostro = !empty($usuario['rostro_descriptor']);
$perfilCompleto = $tieneRostro && !empty($usuario['fecha_nacimiento']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl border border-gray-200 text-center my-8">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
            👋
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-1">¡Hola, <?= e($usuario['nombre']) ?>!</h1>
        <p class="text-emerald-600 font-medium text-sm mb-6">Sesión activa y verificada.</p>

        <?php if (!$perfilCompleto): ?>
            <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl mb-4 text-xs font-bold">
                🔒 Por políticas de seguridad, debes registrar tu Fecha de Nacimiento y Biometría Facial aquí abajo antes de navegar en el portafolio.
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-2.5 rounded-xl mb-4 text-xs font-semibold">
                <?= e($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-2.5 rounded-xl mb-4 text-xs font-semibold">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <ul class="text-left bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-3 mb-6 text-sm">
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Correo:</span> 
                <span class="text-gray-900"><?= e($usuario['correo']) ?></span>
            </li>
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Edad actual:</span> 
                <span class="text-gray-900"><?= e((string)$edadExacta) ?> años</span>
            </li>
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Fecha Nac.:</span> 
                <span class="text-gray-900"><?= e($usuario['fecha_nacimiento'] ?: '⚠️ Pendiente') ?></span>
            </li>
            <li class="flex justify-between border-b border-gray-200 pb-2">
                <span class="font-bold text-gray-600">Carrera:</span> 
                <span class="text-gray-900 text-right"><?= e($usuario['carrera']) ?></span>
            </li>
            <li class="flex justify-between">
                <span class="font-bold text-gray-600">Acceso Facial:</span> 
                <span class="<?= $tieneRostro ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold' ?>">
                    <?= $tieneRostro ? '🟢 Activo (Obligatorio)' : '🔴 Pendiente (Obligatorio)' ?>
                </span>
            </li>
        </ul>

        <!-- FORMULARIO PARA COMPLETAR O ACTUALIZAR FECHA Y ROSTRO -->
        <form method="post" class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 text-left space-y-3">
            <p class="text-xs font-bold text-slate-700 text-center">⚙️ Configurar Fecha de Nacimiento y Biometría</p>
            <input type="hidden" name="rostro_descriptor" id="rostro_descriptor">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required value="<?= e($usuario['fecha_nacimiento'] ?? '') ?>"
                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white">
            </div>

            <div class="text-center">
                <div id="camara-box" class="hidden relative mx-auto w-48 h-36 bg-black rounded-lg overflow-hidden mb-2 border-2 border-indigo-500">
                    <video id="video-perfil" autoplay muted playsinline class="w-full h-full object-cover"></video>
                </div>
                <p id="estado-perfil" class="text-xs text-gray-500 mb-2"></p>
                <button type="button" id="btn-camara-perfil" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2 px-3 rounded-lg">
                    📷 <?= $tieneRostro ? 'Actualizar mi Rostro' : 'Escanear mi Rostro (Obligatorio)' ?>
                </button>
                <button type="button" id="btn-escanear-perfil" class="hidden bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 px-3 rounded-lg">
                    ✨ Capturar Rostro
                </button>
            </div>

            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-all">
                💾 Guardar Cambios en mi Cuenta
            </button>
        </form>

        <div class="flex flex-col gap-3">
            <?php if ($perfilCompleto): ?>
                <a href="index.php" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md">
                    ⬅️ Ir al portafolio principal
                </a>
            <?php endif; ?>
            <a href="logout.php" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md">
                🚪 Cerrar sesión
            </a>
        </div>
    </main>

    <script>
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        const btnCam = document.getElementById('btn-camara-perfil');
        const btnScan = document.getElementById('btn-escanear-perfil');
        const camBox = document.getElementById('camara-box');
        const videoP = document.getElementById('video-perfil');
        const estadoP = document.getElementById('estado-perfil');
        const inputDesc = document.getElementById('rostro_descriptor');
        let streamP = null;

        btnCam.addEventListener('click', async () => {
            estadoP.textContent = '⏳ Cargando IA...';
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);
            streamP = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            videoP.srcObject = streamP;
            camBox.classList.remove('hidden');
            btnCam.classList.add('hidden');
            btnScan.classList.remove('hidden');
            estadoP.textContent = 'Mira de frente y presiona Capturar Rostro.';
        });

        btnScan.addEventListener('click', async () => {
            const det = await faceapi.detectSingleFace(videoP, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
            if (!det) {
                estadoP.textContent = '⚠️ No se detectó rostro. Intenta de nuevo.';
                return;
            }
            inputDesc.value = JSON.stringify(Array.from(det.descriptor));
            if (streamP) streamP.getTracks().forEach(t => t.stop());
            camBox.classList.add('hidden');
            btnScan.classList.add('hidden');
            estadoP.innerHTML = '<span class="text-emerald-600 font-bold">✅ Rostro listo. Presiona "Guardar Cambios en mi Cuenta".</span>';
        });
    </script>
</body>
</html>