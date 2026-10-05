<?php
// ================================================================
// dos_lados.php
// Aplicaciones Web (APW) - Unidad I. Introducción al desarrollo web
// Tema: Conceptos generales del desarrollo web
//
// COMENTARIO DE PHP: este bloque se ejecuta en el SERVIDOR.
// Busca esta frase en "Ver código fuente": ¿aparece?
// ================================================================

$inicio = microtime(true);

// ---- Respuesta ligera para la prueba de ida y vuelta (petición asíncrona) ----
if (isset($_GET['ping'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode([
        'hora_servidor' => date('H:i:s'),
        'zona_servidor' => date_default_timezone_get(),
    ]);
    exit;
}

function dato($clave)
{
    return (isset($_SERVER[$clave]) && $_SERVER[$clave] !== '') ? $_SERVER[$clave] : 'no disponible';
}

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

$es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';

$host_servidor = function_exists('gethostname') ? gethostname() : false;

$servidor = [
    'Software del servidor'          => dato('SERVER_SOFTWARE'),
    'Versión de PHP'                 => PHP_VERSION,
    'Sistema operativo'              => PHP_OS,
    'Nombre del equipo'              => $host_servidor ?: 'no disponible',
    'Dominio solicitado'             => dato('HTTP_HOST'),
    'Puerto'                         => dato('SERVER_PORT'),
    'Cifrado'                        => $es_https ? 'HTTPS (cifrado)' : 'HTTP (sin cifrar)',
    'IP con la que te ve el servidor' => dato('REMOTE_ADDR'),
    'Zona horaria del servidor'      => date_default_timezone_get(),
    'Hora del servidor'              => date('H:i:s'),
];

$linea_peticion = dato('REQUEST_METHOD') . ' ' . dato('REQUEST_URI') . ' ' . dato('SERVER_PROTOCOL');
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dos lados de una petición</title>
<style>
  :root {
    --fondo: #eef1f4;
    --superficie: #ffffff;
    --tinta: #17202b;
    --tenue: #5b6673;
    --linea: #c9d0d8;
    --cliente: #1f5f8b;
    --servidor: #8a4b1f;
    --aviso: #9b1c1c;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    background: var(--fondo);
    color: var(--tinta);
    font: 16px/1.5 system-ui, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }
  .contenedor { max-width: 1120px; margin: 0 auto; padding: 32px 20px 48px; }
  h1 { font-size: 2rem; line-height: 1.15; margin: 0 0 8px; letter-spacing: -0.01em; }
  h2 { font-size: 1.15rem; margin: 0 0 4px; }
  p { margin: 0 0 12px; max-width: 70ch; }
  .intro { color: var(--tenue); }

  .aviso {
    border: 2px solid var(--aviso);
    background: #fff5f5;
    color: var(--aviso);
    padding: 14px 16px;
    margin: 20px 0;
  }
  .aviso strong { display: block; margin-bottom: 4px; }

  /* Tres columnas: cliente | intercambio HTTP | servidor */
  .escena {
    display: grid;
    grid-template-columns: 1fr minmax(220px, 300px) 1fr;
    gap: 0;
    margin: 28px 0;
    background: var(--superficie);
    border: 1px solid var(--linea);
  }
  .lado { padding: 20px; }
  .lado.cliente { border-top: 4px solid var(--cliente); }
  .lado.servidor { border-top: 4px solid var(--servidor); }
  .lado .donde { color: var(--tenue); font-size: 0.9rem; margin-bottom: 14px; }
  .cliente h2 { color: var(--cliente); }
  .servidor h2 { color: var(--servidor); }

  dl { margin: 0; }
  dl div { padding: 7px 0; border-bottom: 1px solid #e6eaee; }
  dl div:last-child { border-bottom: 0; }
  dt { font-size: 0.82rem; color: var(--tenue); }
  dd { margin: 0; word-break: break-word; }

  .intercambio {
    background: #f7f9fb;
    border-left: 1px solid var(--linea);
    border-right: 1px solid var(--linea);
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 18px;
  }
  .mensaje { font-size: 0.8rem; }
  .mensaje .rumbo { color: var(--tenue); margin-bottom: 4px; }
  .mensaje pre {
    margin: 0;
    padding: 10px;
    background: var(--superficie);
    border: 1px solid var(--linea);
    font: 0.78rem/1.45 ui-monospace, Consolas, "Courier New", monospace;
    white-space: pre-wrap;
    word-break: break-all;
  }
  .peticion pre { border-left: 3px solid var(--cliente); }
  .respuesta pre { border-left: 3px solid var(--servidor); }

  .bloque {
    background: var(--superficie);
    border: 1px solid var(--linea);
    padding: 20px;
    margin-top: 20px;
  }
  .bloque .donde { color: var(--tenue); font-size: 0.9rem; margin-bottom: 12px; }

  form { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
  label { font-size: 0.9rem; }
  input[type=text] {
    font: inherit;
    padding: 8px 10px;
    border: 1px solid var(--linea);
    min-width: 220px;
  }
  button {
    font: inherit;
    padding: 8px 14px;
    border: 1px solid var(--tinta);
    background: var(--tinta);
    color: #fff;
    cursor: pointer;
  }
  button:hover { background: #2c3a4b; }
  input:focus-visible, button:focus-visible { outline: 3px solid #7fb0d6; outline-offset: 2px; }

  .resultado { margin-top: 12px; }
  .resultado strong { color: var(--servidor); }
  ol.registro { margin: 12px 0 0; padding-left: 22px; font-size: 0.92rem; }
  ol.registro li { padding: 3px 0; }

  .dos { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .dos .bloque { margin-top: 0; }

  footer { margin-top: 28px; color: var(--tenue); font-size: 0.85rem; }

  @media (max-width: 860px) {
    .escena { grid-template-columns: 1fr; }
    .intercambio { border-left: 0; border-right: 0; border-top: 1px solid var(--linea); border-bottom: 1px solid var(--linea); }
    .dos { grid-template-columns: 1fr; }
  }
</style>
</head>
<body data-offset-servidor="<?= (int) date('Z') ?>">
<div class="contenedor">

  <h1>Dos lados de una petición</h1>
  <p class="intro">Cada vez que abres esta página participan dos programas en dos lugares distintos:
  tu navegador (el cliente) y un servidor web. A la izquierda está lo que calculó tu navegador;
  a la derecha, lo que calculó el servidor antes de enviarte la página.</p>

  <!-- Si el servidor ejecutó PHP, aquí quedará la palabra "ok". Si no, quedará vacío. -->
  <span id="sentinela" hidden><?php echo 'ok'; ?></span>

  <div id="sin-php" class="aviso" hidden>
    <strong>El servidor no ejecutó PHP.</strong>
    Esta página llegó a tu navegador sin pasar por un intérprete de PHP, por eso la columna del servidor está vacía.
    Anota con qué herramienta la abriste y compárala con la ejecución desde un servidor que sí interprete PHP.
  </div>

  <section class="escena" aria-label="Cliente, intercambio HTTP y servidor">

    <div class="lado cliente">
      <h2>Lo que sabe tu navegador</h2>
      <p class="donde">Calculado por JavaScript, en tu computadora, después de recibir la página.</p>
      <dl id="datos-cliente"></dl>
    </div>

    <div class="intercambio">
      <div class="mensaje peticion">
        <div class="rumbo">Petición que envió tu navegador</div>
        <pre><?= e($linea_peticion) ?>

Host: <?= e(dato('HTTP_HOST')) ?>

Accept-Language: <?= e(dato('HTTP_ACCEPT_LANGUAGE')) ?></pre>
      </div>
      <div class="mensaje respuesta">
        <div class="rumbo">Respuesta que envió el servidor</div>
        <pre><?= e(dato('SERVER_PROTOCOL')) ?> <?= (int) http_response_code() === 200 ? '200 OK' : (int) http_response_code() ?>

Content-Type: text/html; charset=utf-8</pre>
      </div>
      <p class="mensaje" id="zona" style="margin:0"></p>
    </div>

    <div class="lado servidor">
      <h2>Lo que sabe el servidor</h2>
      <p class="donde">Calculado por PHP, en el servidor, antes de enviarte la página.</p>
      <dl>
<?php foreach ($servidor as $etiqueta => $valor): ?>
        <div><dt><?= e($etiqueta) ?></dt><dd><?= e($valor) ?></dd></div>
<?php endforeach; ?>
      </dl>
    </div>

  </section>

  <div class="dos">

    <section class="bloque">
      <h2>Una aplicación responde a lo que le envías</h2>
      <p class="donde">Escribe tu nombre y envíalo. Observa cómo cambia la barra de direcciones.</p>
      <form method="get" action="">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="40" value="<?= e($nombre) ?>" autocomplete="off">
        <button type="submit">Enviar al servidor</button>
      </form>
<?php if ($nombre !== ''): ?>
      <p class="resultado">El servidor recibió <strong><?= e($nombre) ?></strong>
      (<?= function_exists('mb_strlen') ? mb_strlen($nombre, 'UTF-8') : strlen($nombre) ?> caracteres) mediante el método <?= e(dato('REQUEST_METHOD')) ?>
      y generó este párrafo a las <?= date('H:i:s') ?>, hora del servidor.</p>
<?php else: ?>
      <p class="resultado">Todavía no has enviado ningún dato.</p>
<?php endif; ?>
    </section>

    <section class="bloque">
      <h2>Ida y vuelta sin recargar la página</h2>
      <p class="donde">Abre las herramientas de desarrollador (F12), pestaña Red o Network, y después pulsa el botón.</p>
      <button type="button" id="btn-ping">Enviar petición</button>
      <ol class="registro" id="registro"></ol>
    </section>

  </div>

  <footer>
    Página generada por el servidor en <?= number_format((microtime(true) - $inicio) * 1000, 2) ?> ms.
  </footer>

</div>

<script>
// COMENTARIO DE JAVASCRIPT: este bloque se ejecuta en el NAVEGADOR (cliente).
// Busca esta frase en "Ver código fuente": ¿aparece?
(function () {
  // 1. ¿El servidor ejecutó PHP?
  var sentinela = document.getElementById('sentinela');
  if (!sentinela || sentinela.textContent.trim() !== 'ok') {
    document.getElementById('sin-php').hidden = false;
  }

  // 2. Datos que conoce el navegador
  var ahora = new Date();
  var zona = (window.Intl && Intl.DateTimeFormat().resolvedOptions().timeZone) || 'no disponible';
  var datos = [
    ['Navegador (User-Agent)', navigator.userAgent],
    ['Idioma del navegador', navigator.language],
    ['Tamaño de pantalla', screen.width + ' × ' + screen.height + ' px'],
    ['Tamaño de la ventana', window.innerWidth + ' × ' + window.innerHeight + ' px'],
    ['Protocolo de la página', location.protocol.replace(':', '').toUpperCase()],
    ['Contexto seguro', window.isSecureContext ? 'sí' : 'no'],
    ['Dominio en la barra de direcciones', location.host || '(ninguno: archivo abierto desde el disco)'],
    ['Zona horaria del equipo', zona],
    ['Hora del equipo', ahora.toLocaleTimeString('es-MX', { hour12: false })]
  ];
  var dl = document.getElementById('datos-cliente');
  datos.forEach(function (par) {
    var fila = document.createElement('div');
    var dt = document.createElement('dt');
    var dd = document.createElement('dd');
    dt.textContent = par[0];
    dd.textContent = par[1];
    fila.appendChild(dt);
    fila.appendChild(dd);
    dl.appendChild(fila);
  });

  // 3. Diferencia de zona horaria entre cliente y servidor
  var offsetServidor = parseInt(document.body.getAttribute('data-offset-servidor'), 10);
  var offsetCliente = -ahora.getTimezoneOffset() * 60;
  var zonaTexto = document.getElementById('zona');
  if (isNaN(offsetServidor)) {
    zonaTexto.textContent = 'Sin datos del servidor para comparar zonas horarias.';
  } else {
    var dif = (offsetServidor - offsetCliente) / 3600;
    zonaTexto.textContent = dif === 0
      ? 'Cliente y servidor usan la misma zona horaria.'
      : 'El reloj del servidor está ' + Math.abs(dif) + ' h ' + (dif > 0 ? 'adelante' : 'atrás') + ' de tu equipo.';
  }

  // 4. Petición asíncrona: el navegador pide datos sin recargar la página
  var n = 0;
  document.getElementById('btn-ping').addEventListener('click', function () {
    var registro = document.getElementById('registro');
    var li = document.createElement('li');
    var t0 = performance.now();
    n += 1;
    fetch(location.pathname + '?ping=1&n=' + n, { cache: 'no-store' })
      .then(function (r) {
        var estado = r.status;
        return r.json().then(function (j) { return { estado: estado, j: j }; });
      })
      .then(function (res) {
        var ms = Math.round(performance.now() - t0);
        li.textContent = 'Respuesta ' + res.estado + ' en ' + ms + ' ms. Hora del servidor: '
          + res.j.hora_servidor + ' (' + res.j.zona_servidor + ').';
      })
      .catch(function () {
        li.textContent = 'La respuesta no llegó en el formato esperado (JSON). Revisa en la pestaña Red qué devolvió el servidor.';
      })
      .then(function () { registro.appendChild(li); });
  });
})();
</script>
</body>
</html>
