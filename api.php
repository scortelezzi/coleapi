<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('func/func.php');
include_once('config/conn.php');

include_once('libs/php-jwt-master/BeforeValidException.php');
include_once('libs/php-jwt-master/ExpiredException.php');
include_once('libs/php-jwt-master/SignatureInvalidException.php');
include_once('libs/php-jwt-master/JWT.php');

$request = $_SERVER['REQUEST_URI']; 
$pieces = explode('/', $request);
$pedido = $pieces[3];

switch ($pedido){
    case 'usuario':
        include('usuario.php');
        break;
        
        case 'login':
        include('login.php');
        break;

    case 'sexo':
        include('sexo.php');
        break;
}

?>