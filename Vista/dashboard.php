<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CRUD Productos Seguro - FISC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container login-container" id="loginSection">

        <div class="card shadow">

            <div class="card-header bg-dark text-white text-center">
                <h4>Autenticación API JWT</h4>
            </div>

            <div class="card-body">

                <form id="loginForm">

                    <div class="mb-3">
                        <label class="form-label">
                            Usuario
                        </label>

                        <input type="text" id="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Contraseña
                        </label>

                        <input type="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-2">

                        Ingresar al Sistema

                    </button>

                    <a href="registrar.php" class="btn btn-success w-100">

                        Registrar Usuario

                    </a>

                </form>

            </div>

        </div>

    </div>

    <div class="container mt-5" id="crudSection">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="text-dark">
                    Panel de Administración
                </h2>

                <small class="text-muted">
                    Conectado de forma segura mediante Bearer Token
                </small>
            </div>

            <button class="btn btn-danger" onclick="cerrarSesion()">

                Cerrar Sesión

            </button>

        </div>

        <div class="card shadow">

            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                <h3 class="mb-0">
                    CRUD DE PRODUCTOS
                </h3>

                <button class="btn btn-light btn-sm" onclick="listarProductos()">

                    Refrescar

                </button>

            </div>

            <div class="card-body">

                <input type="hidden" id="id">

                <div class="row mb-3">

                    <div class="col-md-3">
                        <label class="form-label">
                            Código
                        </label>

                        <input type="text" id="codigo" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Producto
                        </label>

                        <input type="text" id="producto" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Precio
                        </label>

                        <input type="number" step="0.01" id="precio" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Cantidad
                        </label>

                        <input type="number" id="cantidad" min="0" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            &nbsp;
                        </label>

                        <button class="btn btn-success w-100" id="btnGuardar" onclick="guardar()">

                            Guardar

                        </button>

                    </div>

                </div>

                <div class="row mb-4">

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="buscar()">

                            Buscar por Código

                        </button>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-secondary w-100" onclick="limpiar()">

                            Limpiar Campos

                        </button>
                    </div>

                </div>

                <hr>

                <table class="table table-bordered table-hover shadow-sm">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody id="tablaProductos"></tbody>

                </table>

            </div>

        </div>

    </div>

    <script src="js/script.js"></script>

</body>

</html>