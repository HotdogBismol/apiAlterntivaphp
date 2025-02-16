<?php
$servername = "localhost"; 
$username = "pirulit1_usuarioapi"; 
$password = "Contrapirulito@pi"; 
$dbname = "pirulit1_tiendabismol";
$apiToken = "ahhNosHakean";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
