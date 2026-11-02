// Ubicación: Tarea_Render/ubicacion.js

const btnCapturar = document.getElementById('btn-capturar');
const mensaje     = document.getElementById('mensaje');
const datos       = document.getElementById('datos');
const historial   = document.getElementById('historial');

function mostrar(texto, tipo) {
    mensaje.textContent = texto;
    mensaje.className = 'px-4 py-2.5 rounded-xl text-xs font-bold text-center block mb-4 transition-all shadow-sm ';
    if (tipo === 'error') {
        mensaje.className += 'bg-red-50 border border-red-300 text-red-700';
    } else if (tipo === 'exito') {
        mensaje.className += 'bg-emerald-50 border border-emerald-300 text-emerald-700';
    } else {
        mensaje.className += 'bg-blue-50 border border-blue-300 text-blue-700';
    }
    mensaje.hidden = false;
}

// Inicializar Leaflet con mosaicos de OpenStreetMap
const mapa = L.map('mapa').setView([19.43, -99.13], 10);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
}).addTo(mapa);

const capaGuardadas = L.featureGroup().addTo(mapa);
const capaActual    = L.layerGroup().addTo(mapa);

btnCapturar.addEventListener('click', () => {
    if (!window.isSecureContext || !navigator.geolocation) {
        mostrar('La ubicación requiere HTTPS o localhost.', 'error');
        return;
    }
    btnCapturar.disabled = true;
    mostrar('📡 Conectando con satélites GPS / Red...', 'info');
    navigator.geolocation.getCurrentPosition(guardar, fallo, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
    });
});

const errores = {
    1: 'Negaste el permiso de acceso a tu ubicación.',
    2: 'No se pudo determinar tu posición geográfica.',
    3: 'Se agotó el tiempo de espera del sensor GPS.',
};

function fallo(error) {
    btnCapturar.disabled = false;
    mostrar(errores[error.code] ?? error.message, 'error');
}

async function guardar(posicion) {
    const { latitude, longitude, accuracy } = posicion.coords;

    capaActual.clearLayers();
    L.circle([latitude, longitude], { 
        radius: accuracy, 
        color: '#10b981', 
        fillColor: '#34d399', 
        fillOpacity: 0.25 
    }).addTo(capaActual);
    mapa.setView([latitude, longitude], 16);
    mostrarDatos(latitude, longitude, accuracy);

    try {
        const respuesta = await fetch('api/guardar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ latitud: latitude, longitud: longitude, precision: accuracy }),
        });
        const json = await respuesta.json();
        btnCapturar.disabled = false;

        if (!respuesta.ok) {
            mostrar(json.error || 'Error al guardar.', 'error');
            return;
        }
        mostrar('✅ ¡Ubicación GPS registrada en MySQL con éxito!', 'exito');
        cargarHistorial();
    } catch (err) {
        btnCapturar.disabled = false;
        mostrar('Error de conexión con el servidor: ' + err.message, 'error');
    }
}

function mostrarDatos(lat, lng, precision) {
    datos.innerHTML = '';
    const span = document.createElement('span');
    span.textContent = `📍 Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)} (Margen: ±${Math.round(precision)} m) · `;
    
    const enlace = document.createElement('a');
    enlace.href = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=17/${lat}/${lng}`;
    enlace.target = '_blank';
    enlace.className = 'text-blue-600 hover:underline font-bold';
    enlace.textContent = 'Ver en OpenStreetMap ↗';

    datos.appendChild(span);
    datos.appendChild(enlace);
    datos.hidden = false;
}

async function cargarHistorial() {
    try {
        const respuesta = await fetch('api/ubicaciones.php');
        if (!respuesta.ok) return;
        const ubicaciones = await respuesta.json();
        capaGuardadas.clearLayers();
        historial.innerHTML = '';

        for (const u of ubicaciones) {
            const texto = `${u.nombre} · ${u.creado_en}`;
            const etiqueta = document.createElement('div');
            etiqueta.className = 'text-xs font-sans';
            etiqueta.innerHTML = `<strong>${u.nombre}</strong><br><span class="text-gray-500">${u.creado_en}</span><br>±${u.precision_m} m`;

            L.marker([u.latitud, u.longitud]).bindPopup(etiqueta).addTo(capaGuardadas);

            const li = document.createElement('li');
            li.className = 'bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs flex justify-between items-center text-slate-700';
            li.innerHTML = `<span><strong>${u.nombre}</strong> (${u.creado_en})</span> <span class="font-mono text-emerald-600">${u.latitud.toFixed(4)}, ${u.longitud.toFixed(4)} (±${u.precision_m}m)</span>`;
            historial.append(li);
        }
    } catch (e) {
        console.error(e);
    }
}

cargarHistorial().then(() => {
    if (capaGuardadas.getLayers().length) {
        mapa.fitBounds(capaGuardadas.getBounds(), { maxZoom: 16, padding: [25, 25] });
    }
});