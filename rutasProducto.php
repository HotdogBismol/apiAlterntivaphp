<?php
header("Content-Type: application/json");
require 'db.php';

// Obteniendo el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$resource = array_shift($path);
$id = array_shift($path);

// Definiendo las rutas y métodos permitidos
switch ($method) {
    case 'GET':
        if ($resource == 'productos' && $id) {  
            getProducto($id);
        } else {
            getProductos();
        }
        break;
    case 'POST':
        if ($resource == 'productos') {
            addProducto();
        }
        break;
    case 'PUT':
        if ($resource == 'productos' && $id) {
            updateProducto($id);
        }
        break;
    case 'DELETE':
        if ($resource == 'productos' && $id) {
            deleteProducto($id);
        }
        break;
    case 'PATCH':
        if ($resource == 'productos' && $id) {
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
    $data = json_decode(file_get_contents("php://input"), true);
    $nombre = $data['nombre'];
    $precio = $data['precio'];
    $foto = $data['foto'];
    $categoria = $data['categoria'];
    $costo = $data['costo'];
    $descripcion = $data['descripcion'];

    $sql = "INSERT INTO producto (nombre, precio, foto, categoria, costo, descripcion) VALUES ('$nombre', '$precio', '$foto', '$categoria', '$costo', '$descripcion')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Nuevo producto añadido']);
    } else {
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
