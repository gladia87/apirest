<?php

require_once "models/get.model.php";
require_once "models/post.model.php";
require_once "models/put.model.php";
require_once "models/connection.php";

require_once "vendor/autoload.php";
use Firebase\JWT\JWT;

class PostController {

    /*===================================
    Peticiones POST para crear datos
    =====================================*/

    static public function postData($table, $data){

        $response  = PostModel::postData($table, $data);
        
        $return = new PostController();
        $return -> fncResponse($response, null, null);
    }

     /*===================================
    Peticiones POST para registrar usuarios
    =====================================*/

    static public function postRegister($table, $data, $suffix){

        if(isset($data["password_".$suffix]) && $data["password_".$suffix]!=null){
            
            $crypt = crypt($data["password_".$suffix],'$2a$07$azybxcags23425sdg23sdfhsd$');
            
            $data["password_".$suffix]=$crypt;

            $response = PostModel::postData($table, $data);

            $return = new PostController();
            $return -> fncResponse($response, null, $suffix);

        }
    }

     /*===================================
    Peticiones POST para login usuarios
    =====================================*/

    static public function postLogin($table, $data, $suffix){

        $response = GetModel::getDataFilter($table,"*","email_".$suffix,$data["email_".$suffix],null,null,null,null);

      
        if(!empty($response)){

            /*========================
            Encriptamos la contraseña
            ==========================*/
            $crypt = crypt($data["password_".$suffix],'$2a$07$azybxcags23425sdg23sdfhsd$');

            if($response[0]->{"password_".$suffix} == $crypt){
                
                $token = Connection::jwt($response[0]->{"id_".$suffix}, $response[0]->{"password_".$suffix});

                $jwt = JWT::encode($token, "asñkdljañlsdfkwei2i23ls", 'HS256');

                /*========================
                Actualizamos el usuario con el token
                ==========================*/
                $data = array(
                    "token_".$suffix => $jwt,
                    "token_exp_".$suffix => $token["exp"]
                );
                
                $update = PutModel::putData($table, $data, $response[0]->{"id_".$suffix}, "id_".$suffix);
                
                if(isset($update["comment"]) && $update["comment"] == "The process was succesful"){
                    
                    $response[0]->{"token_".$suffix} = $jwt;
                    $response[0]->{"token_exp_".$suffix} = $token["exp"];

                    $return = new PostController();
                    $return -> fncResponse($response, null, $suffix);

                }
                 

            }else{

                $response = null;
                $return = new PostController();
                $return -> fncResponse($response, "Wrong password", $suffix);
            }
          
        }else{
            $response = null;
            $return = new PostController();
            $return -> fncResponse($response, "Wrong email", $suffix);
        }
    }

    /*=========================
    Respuestas del controlador
    ===========================*/

    public function fncResponse($response, $error, $suffix){

        if(!empty($response)){

            /*====================================
            Quitamos la contraseña de la respuesta
            ======================================*/

            if(isset($response[0]->{"password_".$suffix})){

                unset($response[0]->{"password_".$suffix});
            }

            $json = array(
                'status' => 200,               
                'result' => $response
            );

        }else{

            if($error !=null){
                $json = array(
                    'status' => 400,               
                    'result' => $error
                );

            }else{
                $json = array(
                    'status' => 404,
                    'result' => 'Not found',
                    'method' => 'post'
                );
            }
            
        }
        
        
        echo json_encode($json, http_response_code($json['status']));
    }
}