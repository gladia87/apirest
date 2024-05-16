<?php

require_once "get.model.php";

class Connection{

    /*==================================
    Información de la base de datos
    ====================================*/

    static public function infoDatabase(){
        $infoDB = array(
            "database" => "creart_manager",
            "user" => "root",
            "pass" => ""
        );

        return $infoDB;
    }

     /*========================
    API KEY
    ==========================*/

    static public function apikey(){
        return "c5LTA6WPbMwHhEabYu77nN9cn4VcMj";
    }

    /*========================
    Acceso público
    ==========================*/
    static public function publicAccess(){

        $tables = ["usuarios"];
        
        return $tables;
    }



    /*=====================================
    Conexión a la base de datos
    ======================================= */

    static public function connect(){

        try{
            
            $link = new PDO(
                "mysql:host=localhost;dbname=".Connection::infoDatabase()["database"],
                Connection::infoDatabase()["user"],
                Connection::infoDatabase()["pass"]
            );
            
            $link->exec("set names utf8");
            
        }catch(PDOException $e){
            
            die("Error: ".$e->getMessage());

        }

        return $link;
    }

    /*=======================================
    Validar existencia de una tabla en la BD
    =========================================*/

    static public function getColumnsData($table, $columns){

        //Traer el nombre de la Base de Datos
        $database = Connection::infoDatabase()["database"];

        //Traer todas las columnas de una tabla
        $validate = Connection::connect()        
        ->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
        ->fetchAll(PDO::FETCH_OBJ);

       
        //Validamos existencia de la tabla
        if(empty($validate)){
            
            return null;

        }else{

            if($columns[0] == "*"){
                
                array_shift($columns);

            }


            $sum = 0;

            foreach($validate as $key => $value){            
                    $sum += in_array($value->item, $columns);                                
            }

           
            return $sum==count($columns) ? $validate : null;

        }
    }

    /*=======================================
    Generar Token de Autenticación
    =========================================*/

    static public function jwt($id, $email){

        $time = time();

        $token = array(
            
            "iat" => $time,//Tiempo en que inicia el token
            "exp" => $time + (60 *60 * 24), //* 60 *60 * 24 Tiempo en que expirará el token (1 día)
            "data" => [
                "id" => $id,
                "email" => $email
            ]
        );       
        
        return $token;
    }

    /*=======================================
    Validar el Token
    =========================================*/

    static public function tokenValidate($token, $table, $suffix){
        
        $user = GetModel::getDataFilter($table, "token_exp_".$suffix, "token_".$suffix, $token, null, null,null, null);
              
        if(!empty($user)){
            /*=================================
            Validar que el token no ha caducado
            ===================================*/

            $time = time();

            if($time < $user[0]->{"token_exp_".$suffix}){
                
                return "ok";

            }else{

                return "expired";
            }

        }else{

            return "no-auth";
        }

    }

   
}