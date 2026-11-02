// Ubicación: Tarea_Render/foto.js

const video       = document.getElementById('video');
const btnCamara   = document.getElementById('btn-camara');
const btnTomar    = document.getElementById('btn-tomar');
const archivo     = document.getElementById('archivo');
const vistaPrevia = document.getElementById('vista-previa');
const btnSubir    = document.getElementById('btn-subir');
const mensaje     = document.getElementById('mensaje');
const galeria     = document.getElementById('galeria');

let foto = null;
let streamCamara = null;

function mostrar(texto, tipo) {
    mensaje.textContent = texto;
    mensaje.className = 'px-4 py-2.5 rounded-xl text-xs font-bold text-center block my-4 transition-all shadow-sm ';
    if (tipo === 'error') {
        mensaje.className += 'bg-red-50 border border-red-300 text-red-700';
    } else if (tipo === 'exito') {
        mensaje.className += 'bg-emerald-50 border border-emerald-300 text-emerald-700';
    } else {
        mensaje.className += 'bg-blue-50 border border-blue-300 text-blue-700';
    }
    mensaje.hidden = false;
}

function usarFoto(imagen) {
    foto = imagen;
    vistaPrevia.src = URL.createObjectURL(imagen);
    vistaPrevia.hidden = false;
    btnSubir.disabled = false;
    btnSubir.classList.remove('bg-gray-400', 'cursor-not-allowed');
    btnSubir.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
}

// ---------- Opción 1: Cámara en vivo ----------
btnCamara.addEventListener('click', async () => {
    if (!navigator.mediaDevices) {
        mostrar('La cámara solo funciona en https:// o en localhost. Usa la opción de archivo.', 'error');
        return;
    }
    try {
        streamCamara = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        video.srcObject = streamCamara;
        video.hidden = false;
        btnTomar.disabled = false;
        btnTomar.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        btnTomar.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'text-white');
        mostrar('Cámara activa. Encuadra y presiona "Tomar foto".', 'info');
    } catch (error) {
        mostrar('No se pudo abrir la cámara: ' + error.message, 'error');
    }
});

btnTomar.addEventListener('click', () => {
    const lienzo = document.createElement('canvas');
    lienzo.width = video.videoWidth;
    lienzo.height = video.videoHeight;
    lienzo.getContext('2d').drawImage(video, 0, 0);
    lienzo.toBlob((blob) => {
        usarFoto(blob);
        if (streamCamara) {
            streamCamara.getTracks().forEach(t => t.stop());
            video.hidden = true;
        }
        mostrar('📸 Foto capturada desde la cámara. Presiona "Subir foto al servidor" para guardarla.', 'info');
    }, 'image/jpeg', 0.9);
});

// ---------- Opción 2: Archivo del equipo ----------
archivo.addEventListener('change', () => {
    if (archivo.files[0]) {
        usarFoto(archivo.files[0]);
        mostrar('📁 Archivo seleccionado. Presiona "Subir foto al servidor" para guardarlo.', 'info');
    }
});

// ---------- Subir al servicio api/subir.php ----------
btnSubir.addEventListener('click', async () => {
    if (!foto) return;
    const datos = new FormData();
    datos.append('foto', foto, 'foto.jpg');

    btnSubir.disabled = true;
    mostrar('⏳ Subiendo y validando imagen en el servidor...', 'info');

    try {
        const respuesta = await fetch('api/subir.php', { method: 'POST', body: datos });
        const json = await respuesta.json();
        btnSubir.disabled = false;

        if (!respuesta.ok) {
            mostrar(json.error || 'Error al subir la foto.', 'error');
            return;
        }
        mostrar('✅ Foto guardada exitosamente como: ' + json.archivo, 'exito');
        cargarGaleria();
    } catch (err) {
        btnSubir.disabled = false;
        mostrar('Error de red: ' + err.message, 'error');
    }
});

// ---------- Galería: consume api/fotos.php ----------
async function cargarGaleria() {
    try {
        const respuesta = await fetch('api/fotos.php');
        if (!respuesta.ok) return;
        const fotos = await respuesta.json();
        galeria.innerHTML = '';

        if (fotos.length === 0) {
            const vacio = document.createElement('p');
            vacio.className = 'text-xs text-gray-400 col-span-full text-center py-6';
            vacio.textContent = 'Aún no hay fotos en la galería. ¡Sube la primera!';
            galeria.appendChild(vacio);
            return;
        }

        for (const f of fotos) {
            const figura = document.createElement('figure');
            figura.className = 'bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col';

            const img = document.createElement('img');
            img.src = f.url;
            img.alt = 'Foto de ' + f.usuario;
            img.className = 'w-full h-44 object-cover bg-gray-200';

            const pie = document.createElement('figcaption');
            pie.className = 'p-2.5 text-xs font-bold text-gray-700 text-center truncate border-t border-slate-200';
            pie.textContent = '👤 ' + f.usuario; // Prevención estricta de XSS

            figura.append(img, pie);
            galeria.append(figura);
        }
    } catch (e) {
        console.error(e);
    }
}

cargarGaleria();