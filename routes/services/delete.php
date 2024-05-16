<?php

require_once "models/connection.php";
require_once "controllers/delete.controller.php";

if(isset($_GET["id"]) && isset($_GET['nameId'])){
   
    $columns = array($_GET["nameId"]);;

    /*============================================
    Validar tablas y columnas de la BD
    ============================================*/
    if(empty(Connection::getColumnsData($table, $columns))){

        $json = array(
            'status' => 400,
            'result' => 'Error: Los campos en el formulario no coinciden con la base de datos'
        );
    
        echo json_encode($json, http_response_code($json['status']));
        
        return ;
    }

    if(isset($_GET["token"])){

        $tableToken = $_GET["table"] ?? "usuarios";
        $suffix = $_GET["suffix"] ?? "usuario";

        $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);


            /*=======================================================================
            Solicitamos repuesta del controlador para crear datos en cualquier tabla
            =========================================================================*/
        if($validate == "ok"){
            
            /*============================================
            Solicitamos respuesta del controlador para eliminar datos en cualquier tabla
            ============================================*/

            $response = new DeleteController();
            $response -> deleteData($table, $_GET["id"], $_GET["nameId"]);
        }

        /*=======================================================================
        Error cuando el Token ha caducado
        =========================================================================*/
        if($validate=="expired"){

            $json = array(
                'status' => 400,
                'result' => 'Error: El Token ha caducado'
            );
        
            echo json_encode($json, http_response_code($json['status']));
            
            return ;

        }

            /*=======================================================================
            Error cuando el Token no coincide en la BD
            =========================================================================*/
        if($validate=="no-auth"){

            $json = array(
                'status' => 400,
                'result' => 'Error: El usuario no está autorizado'
            );
        
            echo json_encode($json, http_response_code($json['status']));
            
            return ;

        }
    }else{
            /*=======================================================================
            Error cuando no envía el token
            =========================================================================*/

            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización requerida'
            );
        
            echo json_encode($json, http_response_code($json['status']));
            
            return ;

    }
}