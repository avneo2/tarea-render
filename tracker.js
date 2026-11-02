// Ubicación: Tarea_Render/tracker.js
(() => {
    const API = 'api/eventos.php';

    function leerCookie(nombre) {
        const coincide = document.cookie.match(new RegExp('(?:^|; )' + nombre + '=([^;]*)'));
        return coincide ? coincide[1] : null;
    }

    function guardarCookie(nombre, valor, dias) {
        document.cookie = `${nombre}=${valor}; max-age=${dias * 86400}; path=/; SameSite=Lax`;
    }

    function nuevoId() {
        if (window.crypto && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    // ---------- 1. Consentimiento ----------
    function mostrarAviso() {
        const aviso = document.createElement('div');
        aviso.className = 'aviso-cookies fixed bottom-4 left-4 right-4 max-w-xl mx-auto bg-slate-900 text-white p-5 rounded-2xl shadow-2xl border border-slate-700 z-50 flex flex-col sm:flex-row items-center gap-4 text-xs';
        aviso.innerHTML = `
            <p class="flex-1 leading-relaxed text-slate-200">
                🍪 <strong>Aviso de Privacidad:</strong> Usamos cookies para medir cómo se usa este sitio (páginas, clics y tiempo).
                No registramos lo que escribes en los formularios.
            </p>
            <div class="flex gap-2 shrink-0">
                <button type="button" data-respuesta="si" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-xl transition-all">Aceptar</button>
                <button type="button" data-respuesta="no" class="secundario bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold py-2 px-4 rounded-xl transition-all">Rechazar</button>
            </div>`;
        aviso.addEventListener('click', (evento) => {
            const respuesta = evento.target.dataset.respuesta;
            if (!respuesta) return;
            guardarCookie('analitica', respuesta, 180);
            aviso.remove();
            if (respuesta === 'si') iniciar();
        });
        document.body.appendChild(aviso);
    }

    const enlacePrivacidad = document.getElementById('cambiar-privacidad');
    if (enlacePrivacidad) {
        enlacePrivacidad.addEventListener('click', (evento) => {
            evento.preventDefault();
            guardarCookie('analitica', '', 0);
            location.reload();
        });
    }

    const consentimiento = leerCookie('analitica');
    if (consentimiento === 'si') {
        iniciar();
    } else if (consentimiento === null) {
        mostrarAviso();
    }

    // ---------- 2. Identificar y observar ----------
    function iniciar() {
        let visitanteId = leerCookie('visitante_id');
        if (!visitanteId) {
            visitanteId = nuevoId();
        }
        guardarCookie('visitante_id', visitanteId, 365);

        let sesionId = sessionStorage.getItem('sesion_id');
        if (!sesionId) {
            sesionId = nuevoId();
            sessionStorage.setItem('sesion_id', sesionId);
        }

        function enviar(tipo, extra = {}) {
            const evento = {
                tipo,
                visitante_id: visitanteId,
                sesion_id: sesionId,
                pagina: location.pathname,
                ...extra
            };
            navigator.sendBeacon(API, JSON.stringify(evento));
        }

        // a) Vista de página
        enviar('vista');

        // b) Clics en enlaces y botones
        document.addEventListener('click', (evento) => {
            const el = evento.target.closest('a, button, [data-track]');
            if (!el || el.closest('.aviso-cookies')) return;
            const nombre = el.dataset.track || el.textContent.trim().slice(0, 60) || el.tagName;
            enviar('clic', { elemento: nombre });
        });

        // c) Tiempo activo mientras la pestaña está visible
        let inicio = Date.now();
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                const segundos = Math.round((Date.now() - inicio) / 1000);
                if (segundos > 0) enviar('tiempo', { segundos });
            } else {
                inicio = Date.now();
            }
        });
    }
})();