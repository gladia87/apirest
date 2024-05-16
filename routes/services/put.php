<?php

require_once "models/connection.php";
require_once "controllers/put.controller.php";

if(isset($_GET["id"]) && isset($_GET['nameId'])){

    /*=====================================
    Capturamos los datos del formulario
    =======================================*/

    $data = array();

    parse_str(file_get_contents('php://input'), $data);


    /*=====================================
    Separar propiedades en un array
    =======================================*/

    $columns = array();

    foreach(array_keys($data) as $key => $value){
        
        array_push($columns, $value);

    }

    array_push($columns, $_GET["nameId"]);

    $columns = array_unique($columns);


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
        /*=======================================================================
        Petición PUT para usuarios autorizados
        =========================================================================*/
        
        if(isset($_GET["token"])){

            if($_GET["token"]=="no" && isset($_GET["except"])){
                /*============================================
                Validar los campos con las columnas de la BD
                ============================================*/

                $columns = array($_GET["except"]);
                if(empty(Connection::getColumnsData($table, $columns))){

                    $json = array(
                        'status' => 400,
                        'result' => 'Error: Los campos en el formulario no coinciden con la base de datos'
                    );
                
                    echo json_encode($json, http_response_code($json['status']));
                    
                    return ;
                }
                    /*=======================================================================
                    Solicitamos repuesta del controlador para crear datos en cualquier tabla
                    =========================================================================*/
                    $response = new PutController();
                    $response -> putData($table, $data, $_GET["id"], $_GET["nameId"]);
            
            
            }else{

                $tableToken = $_GET["table"] ?? "usuarios";
                $suffix = $_GET["suffix"] ?? "usuario";

                $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);


                    /*=======================================================================
                    Solicitamos repuesta del controlador para crear datos en cualquier tabla
                    =========================================================================*/
                if($validate == "ok"){

                    /*============================================
                    Solicitamos respuesta del controlador para editar datos en cualquier tabla
                    ============================================*/

                    $response = new PutController();
                    $response -> putData($table, $data, $_GET["id"], $_GET["nameId"]);
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