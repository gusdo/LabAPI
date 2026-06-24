<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container registro-container">

        <div class="card shadow">

            <div class="card-header bg-success text-white text-center">
                <h4>Registro de Usuario Administrador</h4>
            </div>

            <div class="card-body">

                <form id="registroForm">

                    <div class="mb-3">
                        <label class="form-label">
                            Usuario
                        </label>

                        <input type="text" id="usuario" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Contraseña
                        </label>

                        <input type="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Confirmar Contraseña
                        </label>

                        <input type="password" id="confirmarPassword" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">

                        Registrar Usuario

                    </button>
                    <div class="mt-3">
                        <a href="dashboard.php" class="btn btn-secondary w-100">
                            Volver al Login
                        </a>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="js/registro.js"></script>

</body>

</html>