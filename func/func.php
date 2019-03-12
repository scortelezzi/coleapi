<?php
use \Firebase\JWT\JWT;

//Arma la respuesta de todos los metodos
//@input $codigo    Codigo HTTP de respuesta
//@input $respuesta Array con las respuestas
function respuesta( $codigo, $respuesta ){
    header('Content-type: application/json');
    echo json_encode( $respuesta );
    http_response_code( $codigo );
}


function limpiarInput( $input ){
    $input = strip_tags( $input );
    $input = htmlspecialchars( $input );

    return $input;
}

function validarToken($token){
    $key = "clAv3Tok3N17031980";
        try{
        $decoded = JWT::decode($token, $key, array('HS256'));
    }

    catch (Exception $e){ 
        http_response_code(401);
     
        $respuesta["ok"] = false;
        $respuesta["mensaje"] = "Acceso no válido";
        $respuesta["respuestaToken"] = $e->getMessage();
        respuesta(401, $respuesta);
        die();
    }
}

?>