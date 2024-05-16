<?php

require_once "connection.php";
require_once "get.model.php";

class DeleteModel{

    /*================================================
    Peticiones DELETE para eliminar datos de forma dinámica
    ==================================================*/

    static public function deleteData($table, $id, $nameId){
        /*================================================
        Validar el Id
        ==================================================*/
        $response = GetModel::getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);

        if(empty($response)){

            $response = array(
            
                "comment" => "Error: The id is not found in the database"
            );

            return $response;

        }

        $sql = "DELETE FROM $table WHERE $nameId = :$nameId";

        $link=Connection::connect();
        $stmt = $link->prepare($sql);

        $stmt->bindParam(":".$nameId, $id, PDO::PARAM_STR);
             
        if($stmt -> execute()){
            
            $response = array(
                "comment" => "The process was succesful"
            );

            return $response;

        }else{
            
          //  echo '<pre>'; print_r($stmt->debugDumpParams()); echo '</pre>';
            return $link->errorInfo();

        }
            
    }
}