const LOGIN_URL = 'http://localhost/LabAPI/Controlador/loginController.php';
const API_URL = 'http://localhost/LabAPI/index.php';

window.onload = function () {
    if (sessionStorage.getItem('jwt_token')) mostrarCRUD();

    const formularioLogin = document.getElementById('loginForm');
    if (formularioLogin) {
        formularioLogin.addEventListener('submit', async (e) => {
            e.preventDefault();
            ejecutarLogin();
        });
    }
};

async function peticionAPI(url, metodo = 'GET', cuerpo = null) {
    const opciones = {
        method: metodo,
        headers: {
            'Authorization': `Bearer ${sessionStorage.getItem('jwt_token')}`,
            'Content-Type': 'application/json'
        }
    };
    if (cuerpo) opciones.body = JSON.stringify(cuerpo);

    try {
        const response = await fetch(url, opciones);
        return await response.json();
    } catch (err) {
        Swal.fire('Error', 'Problema de comunicación con el servidor.', 'error');
        console.error(err);
        return { success: false };
    }
}

function obtenerDatosFormulario() {
    return {
        id: document.getElementById("id").value,
        codigo: document.getElementById("codigo").value.trim(),
        producto: document.getElementById("producto").value.trim(),
        precio: document.getElementById("precio").value,
        cantidad: document.getElementById("cantidad").value
    };
}

async function ejecutarLogin() {
    const usuario = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!usuario || !password) {
        Swal.fire('Atención', 'Por favor, complete todos los campos.', 'warning');
        return;
    }

    try {
        const response = await fetch(LOGIN_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuario, password })
        });
        const data = await response.json();

        if (data.token) {
            sessionStorage.setItem('jwt_token', data.token);
            mostrarCRUD();
            Swal.fire('¡Bienvenido!', 'Autenticación JWT correcta.', 'success');
        } else {
            Swal.fire('Error', data.error || 'Credenciales incorrectas', 'error');
        }
    } catch (err) {
        Swal.fire('Error', 'Error en el servidor de autenticación.', 'error');
    }
}
const REGISTRO_URL ='http://localhost/LabAPI/Controlador/registroController.php';
async function registrarUsuario() {

    const usuario = document.getElementById('usuario').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmarPassword = document.getElementById('confirmarPassword').value.trim();

    if (!usuario || !password || !confirmarPassword) {
        Swal.fire(
            'Atención',
            'Complete todos los campos.',
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

        const response = await fetch(REGISTRO_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario,
                password
            })
        });

        const data = await response.json();

        if (data.success) {

            Swal.fire(
                'Correcto',
                data.message,
                'success'
            );

            document.getElementById('registroForm').reset();

        } else {

            Swal.fire(
                'Error',
                data.message,
                'error'
            );
        }

    } catch (error) {

        Swal.fire(
            'Error',
            'No fue posible registrar el usuario.',
            'error'
        );

        console.error(error);
    }
}

function mostrarCRUD() {
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('crudSection').style.display = 'block';
    listarProductos();
}

function cerrarSesion() {
    sessionStorage.removeItem('jwt_token');
    document.getElementById('crudSection').style.display = 'none';
    document.getElementById('loginSection').style.display = 'block';
}

function validarFormulario() {
    let errores = [];
    const { codigo, producto, precio, cantidad } = obtenerDatosFormulario();

    if (!codigo) errores.push("Debe ingresar un código.");
    if (!producto) errores.push("Debe ingresar un producto.");
    if (!precio || parseFloat(precio) <= 0) errores.push("El precio debe ser mayor que 0.");
    if (!cantidad || parseInt(cantidad) < 0) errores.push("La cantidad no puede ser negativa.");

    return errores;
}

async function guardar() {
    const errores = validarFormulario();
    if (errores.length > 0) {
        Swal.fire({ icon: "error", title: "Errores de validación", html: errores.join("<br>") });
        return;
    }

    const payload = obtenerDatosFormulario();
    const metodo = payload.id ? "PUT" : "POST";

    const data = await peticionAPI(API_URL, metodo, payload);
    if (data.success) {
        Swal.fire("Éxito", data.message || "Operación realizada", "success");
        limpiar();
        listarProductos();
    } else {
        Swal.fire("Error", data.errors ? data.errors.join("<br>") : data.message, "error");
    }
}

async function buscar() {
    const codigo = document.getElementById("codigo").value.trim();
    if (!codigo) {
        Swal.fire('Atención', 'Ingrese un código', 'warning');
        return;
    }

    const data = await peticionAPI(`${API_URL}?codigo=${codigo}`);
    if (data.success && data.data) {
        const p = data.data;
        cargarProducto(p.id, p.codigo, p.producto, p.precio, p.cantidad);
        Swal.fire('Encontrado', p.producto, 'success');
    } else {
        Swal.fire('Aviso', 'Producto no encontrado', 'warning');
    }
}

async function listarProductos() {
    const data = await peticionAPI(API_URL);
    let html = "";

    if (data.success && data.data) {
        data.data.forEach(p => {
            html += `
            <tr>
                <td>${p.id}</td>
                <td><span class="badge bg-info text-dark">${p.codigo}</span></td>
                <td>${p.producto}</td>
                <td>$${parseFloat(p.precio).toFixed(2)}</td>
                <td>${p.cantidad}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm me-2" onclick="cargarProducto('${p.id}', '${p.codigo}', '${p.producto}', '${p.precio}', '${p.cantidad}')">✏️ Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminar(${p.id})">🗑️ Eliminar</button>
                </td>
            </tr>`;
        });
    }
    document.getElementById("tablaProductos").innerHTML = html;
}

function eliminar(id) {
    Swal.fire({
        title: "¿Desea eliminar este producto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No",
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const data = await peticionAPI(`${API_URL}?id=${id}`, "DELETE");
            if (data.success) {
                Swal.fire("Eliminado", data.message, "success");
                listarProductos();
                if (document.getElementById("id").value == id) limpiar();
            }
        }
    });
}

function cargarProducto(id, codigo, producto, precio, cantidad) {
    document.getElementById("id").value = id;
    document.getElementById("codigo").value = codigo;
    document.getElementById("producto").value = producto;
    document.getElementById("precio").value = precio;
    document.getElementById("cantidad").value = cantidad;

    const btn = document.getElementById('btnGuardar');
    btn.textContent = "Actualizar";
    btn.className = "btn btn-warning w-100";
}

function limpiar() {
    document.getElementById("id").value = "";
    document.getElementById("codigo").value = "";
    document.getElementById("producto").value = "";
    document.getElementById("precio").value = "";
    document.getElementById("cantidad").value = "";

    const btn = document.getElementById('btnGuardar');
    btn.textContent = "Guardar";
    btn.className = "btn btn-success w-100";
}