# API RESTful con JWT y CRUD de Productos

Proyecto desarrollado para la asignatura **Desarrollo de Software VII** de la **Universidad Tecnológica de Panamá (UTP)**.

La aplicación implementa una API REST protegida mediante **JSON Web Token (JWT)** y un cliente web desarrollado como una **Single Page Application (SPA)** utilizando JavaScript, Fetch API, Bootstrap y SweetAlert2.

---

# Integrantes

- Aaron Ortiz
- Gustavo Domínguez

**Instructor:** Ing. Irina Fong

---

# Tecnologías utilizadas

- PHP 8
- MySQL
- PDO
- Composer
- firebase/php-jwt
- vlucas/phpdotenv
- Bootstrap 5
- JavaScript (Fetch API)
- SweetAlert2

---

# Arquitectura del proyecto

El proyecto sigue el patrón **Modelo - Vista - Controlador (MVC)** para separar la lógica de negocio, el acceso a datos y la interfaz de usuario.

```
LabAPI/
│
├── Controlador/
│   ├── loginController.php
│   ├── registroController.php
│   └── productoController.php
│
├── Modelo/
│   ├── Conexion.php
│   ├── Producto.php
│   └── Usuario.php
│
├── Vista/
│   ├── dashboard.php
│   ├── registrar.php
│   └── style.css
│
├── Script/
│   ├── script.js
│   └── registro.js
│
├── src/
│   └── Auth/
│       └── AuthService.php
│
├── vendor/
├── .env
├── composer.json
├── composer.lock
└── index.php
```

---

# Base de datos

```sql
CREATE DATABASE productosdb;

USE productosdb;

CREATE TABLE productos(

    id INT AUTO_INCREMENT PRIMARY KEY,

    codigo VARCHAR(50) UNIQUE,

    producto VARCHAR(150),

    precio DECIMAL(10,2),

    cantidad INT

);

CREATE TABLE usuarios(

    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario VARCHAR(100) UNIQUE,

    password VARCHAR(255)

);
```

---

# Dependencias

Instalar las dependencias mediante Composer.

```bash
composer install
```

El proyecto utiliza las siguientes librerías:

```json
{
    "require": {
        "firebase/php-jwt": "^6.0",
        "vlucas/phpdotenv": "^5.6"
    }
}
```

---

# Variables de entorno

Las claves sensibles no se almacenan dentro del código fuente.

Archivo `.env`

```env
JWT_SECRET_KEY="Clave_Secreta_De_Aaron_Para_La_UTP_2026!"
```

El archivo es cargado mediante:

```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();
```

Posteriormente la llave es utilizada para firmar y validar los JWT.

```php
$this->secret_key = $_ENV['JWT_SECRET_KEY'];
```

---

# Seguridad

La aplicación implementa autenticación basada en **JSON Web Token (JWT)** utilizando el algoritmo **HS256**.

El proceso de autenticación es el siguiente:

1. El usuario inicia sesión.
2. El servidor valida las credenciales.
3. Se genera un JWT.
4. El cliente guarda el token en `sessionStorage`.
5. Cada petición protegida envía el encabezado:

```http
Authorization: Bearer TOKEN
```

6. El servidor valida el token antes de permitir el acceso al CRUD.

---

# Registro de usuarios

El sistema permite registrar administradores desde una interfaz independiente.

Las contraseñas **nunca** se almacenan en texto plano.

Se utiliza:

```php
password_hash($password, PASSWORD_BCRYPT);
```

Durante el inicio de sesión se valida mediante:

```php
password_verify($password, $usuarioDB["password"]);
```

Esto garantiza el almacenamiento seguro de las credenciales.

---

# AuthService

La clase `AuthService` encapsula toda la lógica relacionada con JWT.

Funciones principales:

- generarToken()
- validarTokenDesdeCabecera()

El token contiene:

- iss
- aud
- iat
- exp
- id del usuario
- nombre del usuario

La expiración del token es de **1 hora**.

---

# Controladores

## loginController.php

Procesa el inicio de sesión.

Funciones principales:

- recibe usuario y contraseña
- busca el usuario en la base de datos
- verifica la contraseña usando `password_verify()`
- genera un JWT
- devuelve el token al cliente

---

## registroController.php

Permite registrar nuevos usuarios.

Realiza:

- validación de campos
- verificación de usuario existente
- generación del hash con PASSWORD_BCRYPT
- almacenamiento del nuevo usuario

---

## productoController.php

Implementa el CRUD completo de productos.

Soporta los métodos:

- GET
- POST
- PUT
- DELETE

Además realiza validaciones antes de acceder al modelo.

---

# Modelos

## Conexion.php

Centraliza la conexión mediante PDO.

Beneficios:

- reutilización
- manejo de excepciones
- consultas preparadas
- mayor seguridad

---

## Producto.php

Implementa toda la lógica relacionada con la tabla de productos.

Entre sus funciones:

- guardar
- editar
- eliminar
- listar
- buscar
- existeCodigo

---

## Usuario.php

Gestiona la autenticación.

Permite:

- buscar usuarios
- registrar usuarios
- verificar existencia de usuario

---

# Front-End

La interfaz fue desarrollada utilizando:

- Bootstrap
- JavaScript
- Fetch API
- SweetAlert2

La aplicación funciona como una SPA.

El usuario inicia sesión y posteriormente puede realizar todas las operaciones del CRUD sin recargar la página.

---

# Endpoints

## Iniciar sesión

```
POST
http://localhost/LabAPI/Controlador/loginController.php
```

Body

```json
{
    "usuario":"admin",
    "password":"admin123"
}
```

Respuesta

```json
{
    "mensaje":"Autenticación exitosa.",
    "token":"JWT..."
}
```

---

## Registrar usuario

```
POST
http://localhost/LabAPI/Controlador/registroController.php
```

Body

```json
{
    "usuario":"nuevoUsuario",
    "password":"123456"
}
```

---

## Obtener productos

```
GET
http://localhost/LabAPI/index.php
```

Authorization

```
Bearer TOKEN
```

---

## Buscar producto

```
GET
http://localhost/LabAPI/index.php?codigo=P001
```

Authorization

```
Bearer TOKEN
```

---

## Crear producto

```
POST
http://localhost/LabAPI/index.php
```

Authorization

```
Bearer TOKEN
```

Body

```json
{
    "codigo":"P001",
    "producto":"Mouse Logitech",
    "precio":25.50,
    "cantidad":10
}
```

---

## Actualizar producto

```
PUT
http://localhost/LabAPI/index.php
```

Authorization

```
Bearer TOKEN
```

Body

```json
{
    "id":1,
    "codigo":"P001",
    "producto":"Mouse Logitech G",
    "precio":30,
    "cantidad":20
}
```

---

## Eliminar producto

```
DELETE
http://localhost/LabAPI/index.php?id=1
```

Authorization

```
Bearer TOKEN
```

---

# Respuesta estándar de la API

Todas las respuestas mantienen la misma estructura.

```json
{
    "success": true,
    "message": "",
    "data": null,
    "errors": []
}
```

Esto facilita el manejo uniforme de respuestas desde JavaScript.

---

# Características principales

- Arquitectura MVC.
- API REST.
- Autenticación mediante JWT.
- Contraseñas protegidas con PASSWORD_BCRYPT.
- Validación mediante password_verify().
- Variables sensibles almacenadas en `.env`.
- CRUD completo de productos.
- Registro de administradores.
- Consultas preparadas con PDO.
- Protección contra SQL Injection.
- Cliente SPA utilizando Fetch API.
- Mensajes interactivos con SweetAlert2.
- Bootstrap 5 para la interfaz.
