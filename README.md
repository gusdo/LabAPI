
# Informe Técnico: API RESTful Protegida con JWT y Cliente Asíncrono SPA

**Institución:** Universidad Tecnológica de Panamá (UTP)  
**Facultad:** Facultad de Ingeniería de Sistemas Computacionales (FISC)  
**Asignatura:** Desarrollo de Software VII  
**Integrantes:** Aaron Ortiz  y Gustavo Dominguez
**Instructor:** Ing. Irina Fong  
**Semestre:** I Semestre 2026  

---

## 1. Introducción y Fundamentos de la Arquitectura

Este proyecto unifica los objetivos académicos de dos experiencias prácticas de laboratorio:
1. **Desarrollo de un cliente web interactivo** basado en una arquitectura de Aplicación de Una Sola Página (Single Page Application - SPA) que gestiona el ciclo de vida de los datos mediante transferencias asíncronas utilizando la API Fetch.
2. **Construcción de un Backend de servicios desacoplado (Stateless)** protegido perimetralmente mediante el estándar de seguridad JSON Web Tokens (JWT) bajo el estándar RFC 7519.

Al ser una arquitectura sin estado, el servidor no almacena sesiones en memoria (`$_SESSION` está inhabilitado). La legitimidad de cada petición se delega a un mecanismo de autenticación por tokens simétricos firmados digitalmente mediante el algoritmo criptográfico HMAC-SHA256 (HS256). Cada solicitud al recurso protegido del CRUD debe adjuntar dicha firma en los encabezados HTTP; de lo contrario, el acceso es denegado de forma inmediata en el perímetro de la aplicación.

---

## 2. Estructura del Directorio y Configuración del Entorno

El ecosistema de software se organiza siguiendo las directrices profesionales del desarrollo modular con Composer. Se utiliza la especificación de carga automática de clases PSR-4 y la configuración de tipo `project` para representar una aplicación terminada.

```bash
📁 LabAPI/
│
├── 📄 index.php            # Enrutador Central de la API (Validador perimetral de JWT y CORS)
├── 📄 login.php            # Endpoint de Autenticación (Valida credenciales y emite el JWT)
├── 📄 composer.json        # Configuración de dependencias (firebase/php-jwt) y Autoload PSR-4
├── 📄 dashboard.html       # Interfaz Gráfica SPA construida con Bootstrap 5
│
├── 📁 src/
│   └── 📁 Auth/
│       └── 📄 AuthService.php  # Servicio criptográfico de emisión, firma y descifrado de JWT
│
├── 📁 Controlador/
│   └── 📄 productos.php    # Enrutador RESTful del CRUD (Segmenta verbos GET, POST, PUT, DELETE)
│
├── 📁 Modelo/
│   ├── 📄 conexion.php     # Clase DB (Abstracción de conexión nativa segura vía PDO)
│   └── 📄 producto.php     # Clase Producto (Propiedades de entidad y Queries CRUD parametrizados)
│
└── 📁 Vista/
    ├── 📄 script.js        # Controlador Front-End: Manipulación del DOM, Fetch y SweetAlert2

```

### 2.1. Definición del Esquema de Base de Datos (DDL)

La persistencia de los datos se realiza en MySQL, aplicando restricciones de unicidad para garantizar la integridad operacional del catálogo:

```sql
CREATE DATABASE productosdb;
USE productosdb;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    producto VARCHAR(150) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL
);

```

### 2.2. Configuración de Dependencias (`composer.json`)

El archivo de control de dependencias incorpora la librería criptográfica encargada del procesamiento de los tokens:

```json
{
    "name": "aarom/lab-api",
    "description": "API REST con JWT y CRUD Fetch para la UTP",
    "type": "project",
    "require": {
        "firebase/php-jwt": "^6.0"
    },
    "autoload": {
        "psr-4": {
            "Aarom\\LabApi\\": "src/"
        }
    }
}

```

---

## 3. Análisis Técnico de Componentes del Backend

### 3.1. Enrutador Central y Seguridad Perimetral (`index.php`)

Actúa como la aduana de control única del sistema. Intercepta todas las peticiones dirigidas hacia el controlador del CRUD de productos.

* **Políticas CORS Universales:** Inyecta las cabeceras `Access-Control-Allow-*` requeridas para permitir la transferencia de recursos entre orígenes cruzados y mitigar los bloqueos preventivos de los navegadores.
* **Soporte de Peticiones Preflight (`OPTIONS`):** Captura las solicitudes de inspección previa enviadas automáticamente por la API Fetch cuando se adjuntan cabeceras personalizadas. Responde de inmediato con un código `HTTP 200 OK` para autorizar el despacho del verbo real de la transacción.
* **Aislamiento del Controlador:** Invoca el método de validación de tokens antes de incluir el archivo de lógica de negocio. Si la autenticación falla, el hilo de ejecución se detiene de inmediato, impidiendo accesos no autorizados a la capa del controlador.

### 3.2. Servicio Criptográfico (`src/Auth/AuthService.php`)

Encapsula la lógica de firmas digitales bajo el espacio de nombres `Aarom\LabApi\Auth`.

* **`generarToken($usuarioId, $usuarioNombre)`:** Modela los *claims* técnicos requeridos por el estándar RFC 7519:
* `iss` (Emisor) / `aud` (Audiencia): Fijados localmente en `http://localhost`.
* `iat` (*Issued At*): Registro de la marca de tiempo exacta de la firma (`time()`).
* `exp` (*Expiration Time*): Define un límite de expiración estricto de una hora activa (`time() + 3600`).


* **`validarTokenDesdeCabecera()`:** Procesa las solicitudes entrantes normalizando las llaves de las cabeceras a minúsculas mediante `array_change_key_case()` para garantizar la compatibilidad entre diferentes clientes de red. Extrae el prefijo `Bearer`, decodifica el token y, ante cualquier alteración de firmas o expiración de tiempo, captura la excepción, inyecta un código de estado `HTTP 401 Unauthorized` y aborta la ejecución mediante `exit()`.

### 3.3. Procesador de Autenticación (`login.php`)

Endpoint público expuesto para la validación inicial de credenciales de usuario.

* **Restricción Semántica de Red:** Evalúa la propiedad `$_SERVER['REQUEST_METHOD']`. Si la petición no se realiza por el método `POST`, deniega el flujo con un código `HTTP 405 Method Not Allowed`.
* **Decodificación de Flujos JSON:** Recupera los flujos binarios de entrada crudos del servidor web mediante la lectura de la tubería virtual `php://input` y los transforma en matrices asociativas tradicionales con `json_decode(..., true)`.
* **Emisión Controlada:** Compara las propiedades contra las credenciales estáticas asignadas para la experiencia de laboratorio (`admin` / `admin123`). Si los valores coinciden, delega al servicio la firma de un token JWT y responde con un código `HTTP 200 OK`; en caso contrario, emite un error `HTTP 401 Unauthorized`.

### 3.4. Controlador RESTful (`Controlador/productos.php`)

Enruta las intenciones operativas del cliente mapeando los métodos HTTP hacia las subrutinas lógicas correspondientes del Modelo de persistencia:

* **`POST`:** Captura el payload, valida que el código identificador no se encuentre duplicado con `Producto::existeCodigo($codigo)` y procesa la inserción devolviendo un estado `HTTP 201 Created`.
* **`GET`:** Expone un comportamiento dual. Si detecta un parámetro de consulta (`?codigo=...`), ejecuta una lectura selectiva individual (devolviendo `HTTP 404 Not Found` si el artículo no existe). Si se invoca sin parámetros, retorna el catálogo completo del inventario con un estado `HTTP 200 OK`.
* **`PUT`:** Extrae las propiedades modificadas del cuerpo de la solicitud web y procesa la actualización filtrando por el ID interno.
* **`DELETE`:** Procesa la baja física del registro recuperando el ID provisto en la URL.

### 3.5. Capa del Modelo de Persistencia (`Modelo/`)

#### 📄 `conexion.php` (Clase `DB`)

* Centraliza el acceso seguro a la base de datos a través de **PDO (PHP Data Objects)**.
* Activa la directiva `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`. Esto obliga al Backend a transformar los fallos de base de datos en excepciones controladas que se pueden capturar, aislando las credenciales del servidor y neutralizando la impresión accidental de trazas de error en texto plano.

#### 📄 `producto.php` (Clase `Producto`)

* **Inmunización contra Inyecciones SQL:** Los métodos encargados de la mutación de registros (`guardar` y `editar`) implementan de manera estricta **consultas preparadas con marcadores de posición posicionales (`?`)**. Al desvincular los datos de la estructura lógica del query, se neutraliza cualquier intento de secuestro de base de datos.
* **Optimización de Memoria mediante Métodos Estáticos:** Los métodos globales de lectura (`listar`, `buscar`, `existeCodigo`, `eliminar`) están declarados bajo el ámbito **`public static function`**. Esto permite su ejecución directa mediante el operador de resolución de ámbito (`Producto::listar()`) sin necesidad de instanciar un objeto individual en la memoria del servidor, optimizando la velocidad de respuesta de la API REST.

---

## 4. Análisis del Cliente Asíncrono (Front-End SPA)

La interfaz se comporta como una Aplicación de Una Sola Página (SPA). Las transiciones de pantalla se realizan de manera dinámica manipulando las propiedades del DOM del cliente, evitando recargas innecesarias.

### 4.1. Maquetación Modular (`Vista/index.html`)

El documento divide el espacio de trabajo en dos grandes contenedores semánticos controlados por Bootstrap 5:

* **`#loginSection`:** Renderiza de forma prioritaria el formulario de inicio de sesión recopilando los datos del operador del sistema.
* **`#crudSection`:** Panel de administración que permanece inicialmente oculto mediante estilos CSS (`display: none`). Integra los campos de texto del catálogo, un campo oculto destinado al control del identificador (`<input type="hidden" id="id">`), botones de acción reactivos y la tabla estructurada para el despliegue del inventario.

### 4.2. Motor de Interacción de Red (`Vista/js/script.js`)

* **`window.onload`:** Evalúa de manera preventiva el estado de la sesión analizando el almacenamiento local del navegador (`sessionStorage.getItem('jwt_token')`). Si detecta un token activo, omite la sección de acceso inicial e invoca directamente la función `mostrarCRUD()`.
* **Abstracción de Red (`peticionAPI`):** Funciona como una capa intermedia unificada. Construye la estructura de la llamada e inyecta de forma automatizada las cabeceras `'Authorization': Bearer [Token]` extraídas del almacenamiento temporal. Integra además bloques `try/catch` para capturar de forma global caídas de red o fallas en el servidor, desplegando un modal interactivo de SweetAlert2 ante excepciones.
* **Estrategia Inserción/Edición Semántica:** Al procesar el guardado, la función `guardar()` inspecciona el campo oculto `#id`. Si carece de valor, configura la petición asíncrona con el método **`POST`** para registrar un elemento nuevo; si contiene un identificador numérico, conmuta dinámicamente la configuración a un método **`PUT`** para modificar la fila existente de forma transparente.
* **Sincronización del DOM:** La función `listarProductos()` consume la API empleando el método `GET`, procesa la respuesta JSON e itera sobre la colección de objetos inyectando plantillas literales de texto (*Template Strings*) en el nodo `#tablaProductos`, sanitizando los precios al formato numérico correcto mediante `.toFixed(2)`.

---

## 5. Matriz de Validación con Postman (Endpoints Protegidos)

Para garantizar la integridad y seguridad de la API de forma independiente a la interfaz, se definen los siguientes casos de prueba en Postman. Al estar activa la seguridad por tokens, **toda solicitud dirigida al endpoint de productos requiere configurar explícitamente la pestaña *Authorization* seleccionando la opción *Bearer Token* e introduciendo el JWT generado.**

### 5.1. Solicitud Denegada por Token Ausente

* **Método HTTP:** `GET`
* **URL:** `http://localhost/LabAPI/index.php`
* **Configuración:** Pestaña *Authorization* vacía (No Auth).
* **Resultado de Red Esperado:** Código de estado `401 Unauthorized`.
* **JSON de Respuesta:**

```json
{
  "error": "Acceso denegado. Falta el token de seguridad."
}

```

### 5.2. Autenticación y Emisión de Token Exitosa

* **Método HTTP:** `POST`
* **URL:** `http://localhost/LabAPI/login.php`
* **Headers:** `Content-Type: application/json`
* **Cuerpo (Raw JSON):**

```json
{
  "usuario": "admin",
  "password": "admin123"
}

```

* **Resultado de Red Esperado:** Código de estado `200 OK`.
* **JSON de Respuesta:**

```json
{
  "mensaje": "Autenticación exitosa.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyRpc3MiOiJodHRwOi8vbG9jYWxob3N0IiwiYXVkI..."
}

```

### 5.3. Inserción Correcta de Producto (Token Válido)

* **Método HTTP:** `POST`
* **URL:** `http://localhost/LabAPI/index.php`
* **Headers:** * `Authorization: Bearer [PEGAR_TOKEN_JWT_AQUÍ]`
* `Content-Type: application/json`


* **Cuerpo (Raw JSON):**

```json
{
  "codigo": "UTP-60",
  "producto": "Teclado Mecánico Redragon 60%",
  "precio": 45.99,
  "cantidad": 20
}

```

* **Resultado de Red Esperado:** Código de estado `201 Created`.
* **JSON de Respuesta:**

```json
{
  "success": true,
  "message": "Producto guardado correctamente.",
  "data": null,
  "errors": []
}

```

### 5.4. Detección de Código Duplicado (Conflicto de Datos)

* **Método HTTP:** `POST`
* **URL:** `http://localhost/LabAPI/index.php`
* **Headers:** `Authorization: Bearer [TOKEN_JWT]`
* **Cuerpo (Raw JSON):** Reenviar el mismo payload con el código `"UTP-60"`.
* **Resultado de Red Esperado:** Código de estado `409 Conflict`.
* **JSON de Respuesta:**

```json
{
  "success": false,
  "message": "Código duplicado",
  "data": null,
  "errors": [
    "Ya existe un producto con ese código."
  ]
}

```

---

## 6. Estándar de Estructura de Respuestas de la API

La API RESTful unifica y estandariza todas sus salidas bajo una estructura JSON homogénea. Esto permite que el cliente Front-End capture y procese los resultados de manera uniforme, tanto en operaciones exitosas como ante excepciones controladas:

```json
{
  "success": "Variable booleana que define el resultado de la operación (true/false)",
  "message": "Cadena de texto informativa con el resumen del estado del proceso",
  "data": "Carga útil que transporta registros individuales o listas de objetos (null si no aplica)",
  "errors": "Colección indexada de strings que detalla las excepciones o fallos detectados en el servidor"
}
```

