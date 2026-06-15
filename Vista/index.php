<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>CRUD Productos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3 class="text-center">
                CRUD DE PRODUCTOS
            </h3>

        </div>

        <div class="card-body">

            <input type="hidden" id="id">

            <div class="row mb-3">

                <div class="col-md-3">

                    <label>Código</label>

                    <input
                        type="text"
                        id="codigo"
                        class="form-control">

                </div>

                <div class="col-md-3">

                    <label>Producto</label>

                    <input
                        type="text"
                        id="producto"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Precio</label>

                    <input
                        type="number"
                        id="precio"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Cantidad</label>

                    <input
                        type="number"
                        id="cantidad"
                        min="0"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>&nbsp;</label>

                    <button
                        class="btn btn-success w-100"
                        onclick="guardar()">

                        Guardar

                    </button>

                </div>

            </div>

            <div class="row mb-4">

                <div class="col-md-3">

                    <button
                        class="btn btn-primary"
                        onclick="buscar()">

                        Buscar

                    </button>

                </div>

            </div>

            <hr>

            <table class="table table-bordered table-hover">

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

                <tbody id="tablaProductos">

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>