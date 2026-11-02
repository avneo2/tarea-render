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
    <title>Métricas del Sitio - Portafolio Alex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .kpi { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: center; }
        .kpi strong { display: block; font-size: 1.5rem; font-weight: 800; color: #059669; }
        .kpi span { font-size: 0.75rem; color: #64748b; font-weight: 600; }
        .tabla { overflow-x: auto; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; text-align: left; }
        th { background: #f1f5f9; color: #334155; font-weight: 700; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
        tr:last-child td { border-bottom: none; }
        .barra { display: block; height: 6px; background: #10b981; border-radius: 4px; margin-top: 4px; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800 antialiased">
    <main class="bg-white max-w-5xl w-full p-8 rounded-2xl shadow-xl border border-gray-200 my-8">
        <?php include 'menu.php'; ?>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-2">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">📊 Métricas del sitio en vivo</h1>
                <p class="text-xs text-gray-500 mt-1">Se actualiza automáticamente cada 5 segundos vía Fetch JSON.</p>
            </div>
            <span id="estado" class="text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg">Cargando...</span>
        </div>

        <section class="kpis" id="kpis"></section>

        <h2 class="text-base font-bold text-gray-800 mb-2">⚡ Actividad reciente (Tiempo real)</h2>
        <div class="tabla"><table id="tabla-recientes"></table></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h2 class="text-base font-bold text-gray-800 mb-2">📄 Páginas más vistas</h2>
                <div class="tabla"><table id="tabla-paginas"></table></div>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-800 mb-2">🖱️ ¿Dónde hacen clic?</h2>
                <div class="tabla"><table id="tabla-clics"></table></div>
            </div>
        </div>

        <h2 class="text-base font-bold text-gray-800 mb-2">👥 Actividad por Usuarios</h2>
        <div class="tabla"><table id="tabla-usuarios"></table></div>

        <h2 class="text-base font-bold text-gray-800 mb-2">🎓 Actividad por Carrera</h2>
        <div class="tabla"><table id="tabla-carreras"></table></div>
    </main>
    <script src="panel.js"></script>
    <script src="tracker.js"></script>
</body>
</html>