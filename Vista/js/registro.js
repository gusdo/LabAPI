const REGISTRO_URL =
'http://localhost/LabAPI/Controlador/registro.php';

document.addEventListener('DOMContentLoaded', () => {


const formulario =
    document.getElementById('registroForm');

if (formulario) {

    formulario.addEventListener(
        'submit',
        async (e) => {

            e.preventDefault();

            registrarUsuario();
        }
    );
}


});

async function registrarUsuario() {


const usuario =
    document.getElementById('usuario')
        .value
        .trim();

const password =
    document.getElementById('password')
        .value
        .trim();

const confirmarPassword =
    document.getElementById('confirmarPassword')
        .value
        .trim();

if (
    !usuario ||
    !password ||
    !confirmarPassword
) {

    Swal.fire(
        'Atención',
        'Debe completar todos los campos.',
        'warning'
    );

    return;
}

if (password !== confirmarPassword) {

    Swal.fire(
        'Error',
        'Las contraseñas no coinciden.',
        'error'
    );

    return;
}

try {

    const response = await fetch(
        REGISTRO_URL,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario,
                password
            })
        }
    );
    const data = await response.json();

    if (data.success) {

        Swal.fire(
            'Correcto',
            data.message,
            'success'
        );

        document
            .getElementById('registroForm')
            .reset();

    } else {

        Swal.fire(
            'Error',
            data.message,
            'error'
        );
    }

}catch (error) {

    console.error(error);

    Swal.fire(
        'Error',
        error.message,
        'error'
    );
}
}

