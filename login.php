<?php


//Librerias JWT
//include_once 'libs/php-jwt-master/BeforeValidException.php';
//include_once 'libs/php-jwt-master/ExpiredException.php';
//include_once 'libs/php-jwt-master/SignatureInvalidException.php';
//include_once 'libs/php-jwt-master/JWT.php';
use \Firebase\JWT\JWT;


//header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Variables
$tipoUsuario = "";

function usuarioExiste($dni){
    global $conn;
    
    $sql = "SELECT dni FROM intranet WHERE dni = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $dni);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $cantidad = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
   
    if ($cantidad > 0) {
        return true;
    } else {
        return false;
    }
}

function claveOk( $dni, $clave, &$tipoUsuario ){
    global $conn;
    
    $sql = "SELECT i.clave, i.intentosFallidos,u.tipoUsuario FROM intranet i INNER JOIN usuarios u ON i.dni = u.dni WHERE i.dni = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $dni);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $claveBd, $intentos, $tipoUsuario);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($clave == $claveBd) {
        return true;
    } else {
        // Incremento los intentos fallidos
        $intentos++;
        $sql = "UPDATE intranet SET intentosFallidos = ? WHERE dni = ? ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $intentos, $dni);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return false;
    }    
}

function usuarioBloqueado( $dni ){
    global $conn;
    
    $sql = "SELECT intentosFallidos FROM intranet WHERE dni = ?";
    $stmt = mysqli_prepare( $conn, $sql );
    mysqli_stmt_bind_param( $stmt, 'i', $dni );
    mysqli_stmt_execute( $stmt );
    mysqli_stmt_bind_result( $stmt, $intentosFallidos );
    mysqli_stmt_fetch( $stmt );
    mysqli_stmt_close( $stmt );

    //TODO: Poner los intentos fallidos en la configuracion de seguridad de la aplicacion.
    if ($intentosFallidos > 5) {
        return true;
    } else {
        return false;
    }    
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parametros = file_get_contents('php://input');
    parse_str($parametros, $parametros);
    $respuesta = array();

    $dni = limpiarInput($parametros['dni']);
    $clave = limpiarInput($parametros['clave']);
    
    if (!usuarioExiste($dni)){
        $respuesta["ok"] = false;
        $respuesta["titulo"] = "Error";
        $respuesta["mensaje"] = "No existe un usuario con ese DNI";
        respuesta(401, $respuesta);
        die();    
    }

    if (usuarioBloqueado($dni)){
        $respuesta["ok"] = false;
        $respuesta["titulo"] = "Usuario Bloqueado";
        $respuesta["mensaje"] = "El usuario se encuentra bloqueado por intentos fallidos de conexion.";
        respuesta(401, $respuesta);
        die();    
    }

    if (!claveOk( $dni, $clave, $tipoUsuario )){
        $respuesta["ok"] = false;
        $respuesta["titulo"] = "Error";
        $respuesta["mensaje"] = "Clave incorrecta";
        respuesta(401, $respuesta);
        die();    
    }

    // Reinicio a 0 los intentos fallidos
    $intentos = 0;
    $sql = "UPDATE intranet SET intentosFallidos = ? WHERE dni = ? ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $intentos, $dni);
    mysqli_stmt_execute($stmt);

    $key = "clAv3Tok3N17031980";
    $iss = 48000;
    $iss = "http://localhost";
    $aud = "http://localhost";
    $iat = 1356999524;
    $nbf = 1357000000;
    
    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "dni" => $dni,
            "tipoUsuario" => $tipoUsuario
        )
     );

    // Genera el JWT
    $jwt = JWT::encode($token, $key);
    
    //$respuesta = array();
    $respuesta["ok"] = true;
    $respuesta["mensaje"] = "Usuario y clave OK";
    //$respuesta['objeto'] = $parametros;
    $respuesta['jwt'] = $jwt;
    $respuesta['tipo'] = $tipoUsuario;

    respuesta(200, $respuesta);

}

 
?>