window.onload = function () {

    listarProductos();

};

function validarFormulario() {

    let errores = [];

    let codigo =
        document.getElementById("codigo").value.trim();

    let producto =
        document.getElementById("producto").value.trim();

    let precio =
        document.getElementById("precio").value;

    let cantidad =
        document.getElementById("cantidad").value;

    if (codigo === "") {
        errores.push("Debe ingresar un código.");
    }

    if (producto === "") {
        errores.push("Debe ingresar un producto.");
    }

    if (precio === "" || parseFloat(precio) <= 0) {
        errores.push("El precio debe ser mayor que 0.");
    }

    if (cantidad === "" || parseInt(cantidad) < 0) {
        errores.push("La cantidad no puede ser negativa.");
    }

    return errores;
}

function guardar() {

    let errores = validarFormulario();

    if (errores.length > 0) {

        Swal.fire({
            icon: "error",
            title: "Errores de validación",
            html: errores.join("<br>")
        });

        return;
    }

    let accion;

    switch (document.getElementById("id").value) {

        case "":
            accion = "Guardar";
            break;

        default:
            accion = "Modificar";
            break;
    }

    let datos = new FormData();

    datos.append("accion", accion);
    datos.append(
        "id",
        document.getElementById("id").value
    );

    datos.append(
        "codigo",
        document.getElementById("codigo").value
    );

    datos.append(
        "producto",
        document.getElementById("producto").value
    );

    datos.append(
        "precio",
        document.getElementById("precio").value
    );

    datos.append(
        "cantidad",
        document.getElementById("cantidad").value
    );

    fetch("../Controlador/productos.php", {
        method: "POST",
        body: datos
    })
        .then(response => response.json())
        .then(data => {

            if (data.success) {

                Swal.fire({
                    icon: "success",
                    title: data.message
                });

                limpiar();
                listarProductos();

            } else {

                Swal.fire({
                    icon: "error",
                    title: data.message,
                    html: data.errors.join("<br>")
                });

            }

        })
        .catch(error => {

            Swal.fire({
                icon: "error",
                title: "Error",
                text: error
            });

        });
}

function buscar() {

    let codigo =
        document.getElementById("codigo").value;

    if (codigo === "") {

        Swal.fire({
            icon: "warning",
            title: "Ingrese un código"
        });

        return;
    }

    fetch(
        "../Controlador/productos.php?accion=Buscar&codigo=" +
        codigo
    )
        .then(response => response.json())
        .then(data => {

            let producto = data.data;

            if (!producto) {

                Swal.fire({
                    icon: "warning",
                    title: "Producto no encontrado"
                });

                return;
            }

            document.getElementById("id").value =
                producto.id;

            document.getElementById("codigo").value =
                producto.codigo;

            document.getElementById("producto").value =
                producto.producto;

            document.getElementById("precio").value =
                producto.precio;

            document.getElementById("cantidad").value =
                producto.cantidad;

        });
}

function listarProductos() {

    fetch(
        "../Controlador/productos.php?accion=Listar"
    )
        .then(response => response.json())
        .then(data => {

            let html = "";

            data.data.forEach(producto => {

                html += `
                <tr>

                    <td>${producto.id}</td>

                    <td>${producto.codigo}</td>

                    <td>${producto.producto}</td>

                    <td>${producto.precio}</td>

                    <td>${producto.cantidad}</td>

                    <td>

                        <button
                        class="btn btn-warning btn-sm"
                        onclick="cargarProducto(
                            '${producto.id}',
                            '${producto.codigo}',
                            '${producto.producto}',
                            '${producto.precio}',
                            '${producto.cantidad}'
                        )">

                        Editar

                        </button>

                        <button
                        class="btn btn-danger btn-sm"
                        onclick="eliminar(${producto.id})">

                        Eliminar

                        </button>

                    </td>

                </tr>
                `;
            });

            document.getElementById(
                "tablaProductos"
            ).innerHTML = html;

        });
}

function eliminar(id) {

    Swal.fire({
        title: "¿Desea eliminar este producto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No"
    })
        .then((result) => {

            if (result.isConfirmed) {

                let datos = new FormData();

                datos.append("accion", "Eliminar");
                datos.append("id", id);

                fetch(
                    "../Controlador/productos.php",
                    {
                        method: "POST",
                        body: datos
                    }
                )
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {

                            Swal.fire({
                                icon: "success",
                                title: data.message
                            });

                            listarProductos();

                        }

                    });

            }

        });
}

function cargarProducto(
    id,
    codigo,
    producto,
    precio,
    cantidad
) {

    document.getElementById("id").value =
        id;

    document.getElementById("codigo").value =
        codigo;

    document.getElementById("producto").value =
        producto;

    document.getElementById("precio").value =
        precio;

    document.getElementById("cantidad").value =
        cantidad;
}

function limpiar() {

    document.getElementById("id").value = "";
    document.getElementById("codigo").value = "";
    document.getElementById("producto").value = "";
    document.getElementById("precio").value = "";
    document.getElementById("cantidad").value = "";
}