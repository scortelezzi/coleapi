<?php
//TODO: Verificar perfil de usuario ante cada consulta.

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

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    //$parametros = file_get_contents('php://input');
    //parse_str($parametros, $parametros);
    //validarToken($parametros['token']);

    //$sql = "DELETE FROM sexos WHERE id = ?";
    //$stmt = mysqli_prepare($conn, $sql);
    //mysqli_stmt_bind_param($stmt, 'i', $parametros['id']);
    //mysqli_stmt_execute($stmt);
    //mysqli_stmt_store_result($stmt);
    //$resultado = mysqli_stmt_affected_rows($stmt);

    //if($resultado == 0){

    //    $respuesta["ok"] = false;
    //    $respuesta["resultado"] = "No se encontro un sexo con id ".$parametros['id'];
    //    respuesta(401, $respuesta);

    //}

    //if($resultado == 1){

        //$respuesta["ok"] = true;
        //$respuesta["resultado"] = "Se elimino el sexo";
        //respuesta(200, $respuesta);

    //}
    
    $headers = apache_request_headers();
    
    $respuesta["ok"] = true;
    $respuesta["token"] = $headers;
    
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