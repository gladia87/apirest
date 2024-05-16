<?php

require_once "connection.php";

class PostModel{

    /*================================================
    Peticiones POST para crear datos de forma dinámica
    ==================================================*/

    static public function postData($table, $data){

        //echo '<pre>'; print_r($data); echo '</pre>';
        
        $columns ="";
        $params = "";

        foreach($data as $key => $value){
            $columns .= $key.",";
            $params .= ":".$key.",";
        }

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";
        
        $link=Connection::connect();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value){

            $stmt->bindParam(":".$key, $data[$key], PDO::PARAM_STR);
           
        }
             

        if($stmt -> execute()){
            
            $response = array(
                'lastId' => $link->lastInsertId(),
                "comment" => "The process was succesful"
            );

            return $response;

        }else{
            
          //  echo '<pre>'; print_r($stmt->debugDumpParams()); echo '</pre>';
            return $link->errorInfo();

        }
            
    }
}