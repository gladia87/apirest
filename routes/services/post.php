<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if(isset($_POST)){

    $columns = array();

    foreach(array_keys($_POST) as $key => $value){
        
        array_push($columns, $value);

    }
  // echo '<pre>'; print_r(Connection::getColumnsData($table, $columns)); echo '</pre>';

   /*============================================
    Validar los campos con las columnas de la BD
    ============================================*/
    if(empty(Connection::getColumnsData($table, $columns))){

        $json = array(
            'status' => 400,
            'result' => 'Error: Los campos en el formulario no coinciden con la base de datos'
        );
    
        echo json_encode($json, http_response_code($json['status']));
        
        return ;
    }
    $response = new PostController();

    /*=======================================================================
    Petición POST para el REGISTRO de usuarios
    =========================================================================*/
    if(isset($_GET["register"]) && $_GET["register"] == true){

        $suffix = $_GET["suffix"] ?? "usuario";

        $response -> postRegister($table, $_POST, $suffix);


    /*=======================================================================
    Petición POST para el LOGIN de usuarios
    =========================================================================*/
    }else if(isset($_GET["login"]) && $_GET["login"] == true){

        $suffix = $_GET["suffix"] ?? "usuario";

        $response -> postLogin($table, $_POST, $suffix);

    }else{

         /*=======================================================================
        Petición POST para usuarios autorizados
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
                $response -> postData($table, $_POST);

            }else{

                $tableToken = $_GET["table"] ?? "usuarios";
                $suffix = $_GET["suffix"] ?? "usuario";

                $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);


                    /*=======================================================================
                    Solicitamos repuesta del controlador para crear datos en cualquier tabla
                    =========================================================================*/
                if($validate == "ok"){
                    
                    $response -> postData($table, $_POST);

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
                Error cuando se solicita Token para realizar la acción
                =========================================================================*/

            $json = array(
                'status' => 400,
                'result' => 'Error: Autorización requerida'
            );
        
            echo json_encode($json, http_response_code($json['status']));
            
            return ;
        }

       

        
       

    }
}