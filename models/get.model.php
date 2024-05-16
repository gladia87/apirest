<?php

require_once "connection.php";

class GetModel{

    /*================================
    Peticiones GET sin filtro
    ==================================*/
    static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt){
        
        //Validar que la tabla y las columnas existen en la BD
        $selectArray = explode(",",$select);

        if(empty(Connection::getColumnsData($table, $selectArray))){
            return null;
        }

        $sql = "SELECT $select FROM $table";

        if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
            $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode";
        }

        if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        try{

            $stmt -> execute();
            
        }catch(PDOException $Exception){
            
            return null;

        }

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }

    /*================================
    Peticiones GET con filtro
    ==================================*/
    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt){

        //Validar que la tabla existe en la BD
        $linkToArray = explode(",", $linkTo);
        $selectArray = explode(",",$select);

        foreach($linkToArray as $key => $value){
             array_push($selectArray, $value);
        }       
      
        $selectArray = array_unique($selectArray);

        if(empty(Connection::getColumnsData($table, $selectArray))){
            return null;
        }
       
       
        $equalToArray = explode(",",$equalTo);
        $linkToText = "";

        if(count($linkToArray)>1){
            foreach($linkToArray as $key => $value){
                if($key > 0){
                    $linkToText .="AND ".$value." = :".$value." ";
                }
            }
        }
        //SIN ORDENAR Y SIN LIMITAR DATOS
        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";

        //ORDENAR DATOS SIN LIMITES
        if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
        }

        //ORDENAR Y LIMITAR DATOS
        if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        //SIN ORDENAR Y LIMITAR DATOS
        if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText  LIMIT $startAt, $endAt";
        }
       
        $stmt = Connection::connect()->prepare($sql);

        foreach($linkToArray as $key => $value){
            $stmt -> bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
        }

        try{

            $stmt -> execute();
            
        }catch(PDOException $Exception){
            
            return null;

        }

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }

    /*================================================
    Peticiones GET sin filtro con tablas relacionadas
    =================================================*/
    static public function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt){

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if(count($relArray)>1){

            foreach($relArray as $key => $value){

                //Validar que la tabla existe en la BD
                if(empty(Connection::getColumnsData($value, ["*"]))){
                    return null;
                }

                if($key > 0){

                    $innerJoinText .=" INNER JOIN ".$value." ON ".$relArray[0].".Id_".$typeArray[$key]."_".$typeArray[0]." = ".$value."Id_".$typeArray[$key]." ";

                }
            }
        

            $sql = "SELECT $select FROM $relArray[0] $innerJoinText";



            if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode";
            }

            if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
            }

            if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText LIMIT $startAt, $endAt";
            }

            $stmt = Connection::connect()->prepare($sql);

            try{

                $stmt -> execute();

            }catch(PDOException $Exception){
                
                return null;

            }
            

            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        
        }else{

            return null;
        }
    }

    /*================================================
    Peticiones GET con filtro con tablas relacionadas
    =================================================*/
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt){

        /*================================================
        Organizamos los filtros
        =================================================*/
        $linkToArray = explode(",", $linkTo);
        $equalToArray = explode(",",$equalTo);
        $linkToText = "";

        if(count($linkToArray)>1){
            foreach($linkToArray as $key => $value){               

                if($key > 0){
                    $linkToText .="AND ".$value." = :".$value." ";
                }
            }
        }
        /*================================================
        Organizamos las relaciones
        =================================================*/
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if(count($relArray)>1){

            foreach($relArray as $key => $value){

                //Validar que la tabla existe en la BD
                if(empty(Connection::getColumnsData($value, ["*"]))){
                    return null;
                }

                if($key > 0){

                    $innerJoinText .=" INNER JOIN ".$value." ON ".$relArray[0].".Id_".$typeArray[$key]."_".$typeArray[0]." = ".$value."Id_".$typeArray[$key]." ";

                }
            }
        
            //SIN ORDENAR Y SIN LIMITAR DATOS
            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";


            //ORDENAR DATOS SIN LIMITES
            if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
            }

            //LIMITAR DATOS Y ORDENAR
            if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
            }

            //LIMITAR DATOS SIN ORDENAR
            if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $startAt, $endAt";
            }

            $stmt = Connection::connect()->prepare($sql);

            foreach($linkToArray as $key => $value){
                $stmt -> bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
            }
    

            try{

                $stmt -> execute();
                
            }catch(PDOException $Exception){
                
                return null;

            }

            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        
        }else{

            return null;
        }
    }

    /*=================================================
    Peticiones GET para el buscador sin relaciones
    ===================================================*/
    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt){
       
        //Validar que la tabla y las columnas existen en la BD
        $selectArray = explode(",",$select);

        if(empty(Connection::getColumnsData($table, $selectArray))){
            return null;
        }

        $linkToArray = explode(",", $linkTo);
        $searchToArray = explode(",",$search);
        $linkToText = "";

        if(count($linkToArray)>1){
            foreach($linkToArray as $key => $value){
                
                if($key > 0){
                    $linkToText .="AND ".$value." = :".$value." ";
                }
            }
        }

        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";

        if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
        }

        if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        foreach($linkToArray as $key => $value){

            if($key > 0){

                $stmt -> bindParam(":".$value, $searchToArray[$key], PDO::PARAM_STR);

            }
                        
        }

        try{

            $stmt -> execute();
            
        }catch(PDOException $Exception){
            
            return null;

        }

        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }

    /*=================================================
    Peticiones GET para seleccion de rangos
    ===================================================*/
    static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo){
         //Validar que la tabla y las columnas existen en la BD
        $linkToArray = explode(",", $linkTo);

        if($filterTo!=null){
            $filterToArray = explode(",", $filterTo);
        }else{
            $filterToArray = array();
        }
       
        $selectArray = explode(",", $select);

        foreach($linkToArray as $key => $value){
            array_push($selectArray, $value);
        }

        foreach($filterToArray as $key => $value){
            array_push($selectArray, $value);
        }
       
        $selectArray = array_unique($selectArray);

        if(empty(Connection::getColumnsData($table, $selectArray))){
            return null;
        }
        
        $filter = "";

        if($filterTo !=null && $inTo != null){
            $filter = 'AND '.$filterTo.' IN ('.$inTo.')';
        }

        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";

        if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
        }

        if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
        }

        if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $startAt, $endAt";
        }

        $stmt = Connection::connect()->prepare($sql);

        try{

            $stmt -> execute();
            
        }catch(PDOException $Exception){
            
            return null;

        }

        return $stmt -> fetchAll(PDO::FETCH_CLASS);


    }

    /*=================================================
    Peticiones GET para seleccion de rangos con relaciones
    ===================================================*/
    static public function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode,$startAt, $endAt, $filterTo, $inTo){

        $linkToArray = explode(',', $linkTo);
        $filterToArray = explode(",", $filterTo);
    
        $filter = "";        

        if($filterTo !=null && $inTo != null){
            $filter = 'AND '.$filterTo.' IN ('.$inTo.')';
        }

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if(count($relArray)>1){

            foreach($relArray as $key => $value){

                //Validar que la tabla existe en la BD
                if(empty(Connection::getColumnsData($value, ["*"]))){
                    return null;
                }

                if($key > 0){

                    $innerJoinText .=" INNER JOIN ".$value." ON ".$relArray[0].".Id_".$typeArray[$key]."_".$typeArray[0]." = ".$value."Id_".$typeArray[$key]." ";

                }
            }

            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";

            if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
            }

            if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $startAt, $endAt";
            }

            if($orderBy == null && $orderMode == null && $startAt != null && $endAt != null){
                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $startAt, $endAt";
            }

            $stmt = Connection::connect()->prepare($sql);

            try{

                $stmt -> execute();
                
            }catch(PDOException $Exception){
                
                return null;
    
            }

            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        }else{

            return null;
        }

    }

}