<?php
include('func/func.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST11";
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    echo "PUT";
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    echo "DELETE";
    $entityBody = file_get_contents('php://input');

    
    $respuesta = array();
    $respuesta["ok"] = true;
    $respuesta["mensaje"] = "Usuario eliminado correctamente";

    respuesta(200, $respuesta);

}




?>