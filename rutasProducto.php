<?php
header("Content-Type: application/json");
require 'db.php';

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
    $nombre = $_GET['nombre'];
    $precio = $_GET['precio'];
    $foto = $_GET['foto'];
    $categoria = $_GET['categoria'];
    $costo = $_GET['costo'];
    $descripcion = $_GET['descripcion'];

    $sql = "INSERT INTO producto (nombre, precio, foto, categoria, costo, descripcion) VALUES ('$nombre', '$precio', '$foto', '$categoria', '$costo', '$descripcion')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Nuevo producto añadido']);
    } else {
        error_log("Error al agregar el producto: " . $conn->error); // Registro del error
        echo json_encode(['message' => 'Error: ' . $conn->error]);
    }
}

function updateProducto($id) {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $nombre = $data['nombre'];
    $precio = $data['precio'];
    $foto = $data['foto'];
    $categoria = $data['categoria'];
    $costo = $data['costo'];
    $descripcion = $data['descripcion'];

    $sql = "UPDATE producto SET nombre='$nombre', precio='$precio', foto='$foto', categoria='$categoria', costo='$costo', descripcion='$descripcion' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Producto actualizado']);
    } else {
        echo json_encode(['message' => 'Error: ' . $conn->error]);
    }
}

function deleteProducto($id) {
    global $conn;
    $sql = "DELETE FROM producto WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Producto eliminado']);
    } else {
        echo json_encode(['message' => 'Error: ' . $conn->error]);
    }
}

function updateStockProducto($id) {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $stock = $data['stock'];

    $sql = "UPDATE producto SET stock='$stock' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Stock del producto actualizado']);
    } else {
        echo json_encode(['message' => 'Error: ' . $conn->error]);
    }
}
?>
