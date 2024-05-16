<?php
/*======================
Mostrar errores
========================*/

ini_set('display_errors',1);
ini_set("log_errors",1);
ini_set("error_log", "C:/wamp64/www/apirest/php_error_log");

/*=========================
CORS
===========================*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method, Access-Control-Request-Headers');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('content-type: application/json; charset=utf-8');


/*=======================
Requerimientos 
=========================*/
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
require_once "controllers/routes.controller.php";

$index = new RoutesController();
$index -> index();