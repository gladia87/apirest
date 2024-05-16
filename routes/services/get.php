<?php

require_once "controllers/get.controller.php";


$select = $_GET["select"] ?? "*";
$orderBy = $_GET["orderBy"] ?? null;
$orderMode = $_GET["orderMode"] ?? null;
$startAt = $_GET["startAt"] ?? null;
$endAt = $_GET["endAt"] ?? null;
$filterTo = $_GET["filterTo"] ?? null;
$inTo = $_GET["inTo"] ?? null;

$response = new GetController();

        
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
        /*===================================
        Peticiones GET con filtro
        =====================================*/

        if(isset($_GET["linkTo"]) && isset($_GET["equalTo"]) && !isset($_GET["rel"]) && !isset($_GET["type"])){

            $response -> getDataFilter($table, $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode,$startAt, $endAt);


            //Peticiones GET sin filtro entre tablas relacionadas
        }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && !isset($_GET["linkTo"]) && !isset($_GET["equalTo"])){
            
            $response -> getRelData($_GET["rel"], $_GET["type"], $select, $orderBy, $orderMode, $startAt, $endAt);

            //Peticiones GET con filtro entre tablas relacionadas
        }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["equalTo"])){

            $response -> getRelDataFilter($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);

            //Peticiones GET para buscador sin relaciones
        }else if(isset($_GET["linkTo"]) && isset($_GET["search"])){    

            $response -> getDataSearch($table, $select, $_GET["linkTo"], $_GET["search"], $orderBy, $orderMode,$startAt, $endAt);

            //Peticiones GET para selección de rangos
        }else if(!isset($_GET["rel"]) && !isset($_GET["type"]) && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])){    

            $response -> getDataRange($table, $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo);

            //Peticiones GET para selección de rangos con tablas relacionadas
        }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])){    

            $response -> getRelDataRange($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo);

        }else{
            //echo "<pre>"; print_r("hola"); echo "</pre>";
        // return;
            $response -> getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);

        }

    }else{

        $tableToken = $_GET["table"] ?? "usuarios";
        $suffix = $_GET["suffix"] ?? "usuario";

        $validate = Connection::tokenValidate($_GET["token"], $tableToken, $suffix);


            /*=======================================================================
            Solicitamos repuesta del controlador para crear datos en cualquier tabla
            =========================================================================*/
        if($validate == "ok"){
                /*===================================
                Peticiones GET con filtro
                =====================================*/

                if(isset($_GET["linkTo"]) && isset($_GET["equalTo"]) && !isset($_GET["rel"]) && !isset($_GET["type"])){

                    $response -> getDataFilter($table, $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode,$startAt, $endAt);


                    //Peticiones GET sin filtro entre tablas relacionadas
                }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && !isset($_GET["linkTo"]) && !isset($_GET["equalTo"])){
                    
                    $response -> getRelData($_GET["rel"], $_GET["type"], $select, $orderBy, $orderMode, $startAt, $endAt);

                    //Peticiones GET con filtro entre tablas relacionadas
                }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["equalTo"])){

                    $response -> getRelDataFilter($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["equalTo"], $orderBy, $orderMode, $startAt, $endAt);

                    //Peticiones GET para buscador sin relaciones
                }else if(isset($_GET["linkTo"]) && isset($_GET["search"])){    

                    $response -> getDataSearch($table, $select, $_GET["linkTo"], $_GET["search"], $orderBy, $orderMode,$startAt, $endAt);

                    //Peticiones GET para selección de rangos
                }else if(!isset($_GET["rel"]) && !isset($_GET["type"]) && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])){    

                    $response -> getDataRange($table, $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo);

                    //Peticiones GET para selección de rangos con tablas relacionadas
                }else if(isset($_GET["rel"]) && isset($_GET["type"]) && $table == "relations" && isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])){    

                    $response -> getRelDataRange($_GET["rel"], $_GET["type"], $select, $_GET["linkTo"], $_GET["between1"], $_GET["between2"], $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo);

                }else{
                    //echo "<pre>"; print_r("hola"); echo "</pre>";
                // return;
                    $response -> getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);

                }


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

    
