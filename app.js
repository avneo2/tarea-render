const formRegistro = document.getElementById('form-registro');

if (formRegistro) {
    formRegistro.addEventListener('submit', (evento) => {
        const password = formRegistro.password.value;
        const confirmar = formRegistro.confirmar.value;
        const descriptor = document.getElementById('rostro_descriptor').value;

        if (password !== confirmar) {
            evento.preventDefault();
            alert('Las contraseñas no coinciden.');
            return;
        }

        if (!descriptor || descriptor.length < 200) {
            evento.preventDefault();
            alert('Es obligatorio escanear tu rostro biométrico antes de registrarte.');
        }
    });
}