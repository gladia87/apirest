<?php

require_once "models/put.model.php";

class PutController {

    /*===================================
    Peticiones PUT para crear datos
    =====================================*/

    static public function putData($table, $data, $id, $nameId){

        $response  = PutModel::putData($table, $data, $id, $nameId);
        
        $return = new PutController();
        $return -> fncResponse($response);
    }

    /*=========================
    Respuestas del controlador
    ===========================*/

    public function fncResponse($response){

        if(!empty($response)){

            $json = array(
                'status' => 200,               
                'result' => $response
            );

        }else{
            $json = array(
                'status' => 404,
                'result' => 'Not found',
                'method' => 'put'
            );
        }
        
        
        echo json_encode($json, http_response_code($json['status']));
    }
}