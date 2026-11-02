<?php
// Barra de navegación superior para todas las vistas protegidas
$paginaActual = basename($_SERVER['PHP_SELF']);
function claseMenu($archivo, $actual) {
    return $archivo === $actual
        ? 'bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold shadow-sm'
        : 'text-gray-600 hover:text-emerald-600 hover:bg-gray-100 px-3 py-1.5 rounded-lg font-semibold transition-all';
}
?>
<nav class="menu flex flex-wrap items-center justify-between gap-2 bg-slate-50 border border-gray-200 p-2.5 rounded-xl mb-6 text-xs">
    <div class="flex flex-wrap items-center gap-1">
        <a href="index.php" class="<?= claseMenu('index.php', $paginaActual) ?>" data-track="Menú: Portafolio">🏠 Portafolio</a>
        <a href="bienvenida.php" class="<?= claseMenu('bienvenida.php', $paginaActual) ?>" data-track="Menú: Inicio">👤 Inicio</a>
        <a href="cursos.php" class="<?= claseMenu('cursos.php', $paginaActual) ?>" data-track="Menú: Cursos">📚 Cursos</a>
        <a href="dashboard.php" class="<?= claseMenu('dashboard.php', $paginaActual) ?>" data-track="Menú: Métricas">📊 Métricas</a>
    </div>
    <div class="flex items-center gap-2">
        <a href="#" id="cambiar-privacidad" class="text-amber-600 hover:underline font-bold px-2 py-1">🍪 Privacidad</a>
        <a href="logout.php" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg font-bold transition-all" data-track="Menú: Salir">🚪 Salir</a>
    </div>
</nav>