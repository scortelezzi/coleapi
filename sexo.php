<?php
include_once('func/func.php');
include_once('config/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $parametros = file_get_contents('php://input');
    parse_str($parametros, $parametros);
    validarToken($parametros['token']);

    $sql = "SELECT id, sexo, fechaAlta, fechaModi, idUsrAlta, idUsrModi FROM sexos ORDER BY id";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $sexo, $fechaAlta, $fechaModi, $idUsrAlta, $idUsrModi);
    
    while (mysqli_stmt_fetch($stmt)) {
        $sexos[] = array("id" => $id,
                         "sexo" => $sexo, 
                         "fechaAlta" => $fechaAlta,
                         "fechaModi" => $fechaModi,
                         "idUsrAlta" => $idUsrAlta,
                         "idUsrModi" => $idUsrModi);
    }


    $sql = "SELECT COUNT(*) AS total FROM sexos";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);

    if($total <= 0){
        $sexos = "No se encontraron resultados";    
    }
    

    $respuesta["ok"] = true;
    $respuesta["sexos"] = $sexos;
    $respuesta["total"] = $total;
    respuesta(200, $respuesta);
}


/*
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
*/



?>