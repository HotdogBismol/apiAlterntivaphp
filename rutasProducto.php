<?php
ob_start();
header("Content-Type: application/json");
require 'db.php';

error_log("Iniciando script"); // Registro de depuración

// Obteniendo el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$resource = array_shift($path);
$id = array_shift($path);

// Autenticación con Token
$headers = apache_request_headers();
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    list($tokenType, $token) = explode(" ", $authHeader, 2);

    if ($tokenType !== 'Bearer' || $token !== $apiToken) {
        header('HTTP/1.0 403 Forbidden');
        echo json_encode(['message' => 'Token inválido']);
        exit;
    }
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['message' => 'Autenticación requerida']);
    exit;
}

error_log("Método: $method, Recurso: $resource, ID: $id"); // Registro de depuración

// Definiendo las rutas y métodos permitidos
switch ($method) {
    case 'GET':
        if ($resource == 'gProductos' && $id) {  
            getProducto($id);
        } else {
            getProductos();
        }
        break;
    case 'POST':
        if ($resource == 'pProductos') {
            addProducto();
        }
        break;
    case 'PUT':
        if ($resource == 'eProductos' && $id) {
            updateProducto($id);
        }
        break;
    case 'DELETE':
        if ($resource == 'dProductos' && $id) {
            deleteProducto($id);
        }
        break;
    case 'PATCH':
        if ($resource == 'sProductos' && $id) {
            updateStockProducto($id);
        }
        break;
    default:
        echo json_encode(['message' => 'Método no permitido']);
        break;
}

// Funciones para cada operación
function getProductos() {
    global $conn;
    error_log("Ejecutando getProductos"); // Registro de depuración
    $sql = "SELECT * FROM producto";
    $result = $conn->query($sql);

    $productos = [];
    while($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode($productos);
}

function getProducto($id) {
    global $conn;
    error_log("Ejecutando getProducto con ID: $id"); // Registro de depuración
    $sql = "SELECT * FROM producto WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        echo json_encode($producto);
    } else {
        echo json_encode(['message' => 'Producto no encontrado']);
    }
}

function addProducto() {
    global $conn;

    // Obtención de parámetros desde la URL
    $nombre = $_GET['nombre'] ?? null;
    $precio = $_GET['precio'] ?? null;
    $foto = $_GET['foto'] ?? null;
    $categoria = $_GET['categoria'] ?? null;
    $costo = $_GET['costo'] ?? null;
    $descripcion = $_GET['descripcion'] ?? null;

    // Registro de los parámetros recibidos
    error_log("Parámetros recibidos - Nombre: $nombre, Precio: $precio, Foto: $foto, Categoría: $categoria, Costo: $costo, Descripción: $descripcion");

    // Verificación de parámetros
    if ($nombre && $precio && $foto && $categoria && $costo && $descripcion) {
        $sql = "INSERT INTO producto (nombre, precio, foto, categoria, costo, descripcion) VALUES ('$nombre', '$precio', '$foto', '$categoria', '$costo', '$descripcion')";
        error_log("SQL: $sql"); // Registro de depuración
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['message' => 'Nuevo producto añadido']);
        } else {
            error_log("Error al agregar el producto: " . $conn->error); // Registro del error
            echo json_encode(['message' => 'Error: ' . $conn->error]);
        }
    } else {
        error_log("Faltan parámetros necesarios");
        echo json_encode(['message' => 'Faltan parámetros necesarios']);
    }
}
